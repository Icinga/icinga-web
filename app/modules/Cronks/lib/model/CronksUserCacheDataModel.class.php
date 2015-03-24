<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
//
// Copyright (c) 2009-2015 Icinga Developer Team.
// All rights reserved.
//
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}

class CronksUserCacheDataModel extends CronksBaseModel {

    /**
     * Additional cache name
     *
     * @var string
     */
    private $uniqueCacheIdentifier = 'UserCacheData';

    /**
     * User
     *
     * @var AppKitSecurityUser
     */
    private $user;

    /**
     * Where to store
     *
     * @var string
     */
    private $cacheDir;

    public function initialize(AgaviContext $context, array $parameters = array()) {
        parent::initialize($context, $parameters);
        IcingaDoctrineDatabase::resetCurrentConnection();

        $this->user = $this->getContext()->getUser();

        if (AgaviConfig::get('core.data_dir') !== null) {
            $this->cacheDir = AgaviConfig::get('core.data_dir') . '/tmp';
        } else {
            $this->cacheDir = '/tmp';
        }

        if (!is_dir($this->cacheDir)) {
            throw new AppKitModelException(
                'Cache dir for CronksUserCacheDataModel does not exist: '
                . $this->cacheDir
            );
        }
    }

    protected function setUniqueCacheIdentifier($identifier) {
        $this->uniqueCacheIdentifier = $identifier;
    }

    private function getCacheKey() {
        return $this->uniqueCacheIdentifier . ':'
            . $this->user->getNsmUser()->user_id;
    }

    private function getCacheFile() {
        $prefix = 'cache_' . $this->uniqueCacheIdentifier . '_'
            . $this->user->getNsmUser()->user_name . '_'
            . sha1($this->getCacheKey())
            . '.json';
        return $this->cacheDir . '/' . $prefix;
    }

    protected function writeData($data) {
        $file = $this->getCacheFile();
        $dataString = json_encode($data);
        file_put_contents($file, $dataString);
    }

    protected function retrieveData($noAssoc = false) {
        $file = $this->getCacheFile();
        if (file_exists($file)) {
            $content = file_get_contents($file);
            if ($content) {
                return json_decode($content, !$noAssoc);
            }
        }

        return null;
    }
} 