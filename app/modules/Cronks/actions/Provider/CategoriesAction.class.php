<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
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


class Cronks_Provider_CategoriesAction extends CronksBaseAction {

    /**
     * Our categories model
     * @var Cronks_Provider_CronkCategoryDataModel
     */
    private $categories = null;

    /**
     * @var NsmUser
     */
    private $user = null;

    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);

        $this->user = $this->getContext()->getUser()->getNsmUser();
        
        $this->categories = $this->getContext()->getModel('Provider.CronkCategoryData', 'Cronks');
    }

    /**
     * Returns the default view if the action does not serve the request
     * method used.
     *
     * @return     mixed <ul>
     *                     <li>A string containing the view name associated
     *                     with this action; or</li>
     *                     <li>An array with two indices: the parent module
     *                     of the view to be executed and the view to be
     *                     executed.</li>
     *                   </ul>
     */
    public function getDefaultViewName() {
        return 'Success';
    }

    public function executeRead(AgaviParameterHolder $rd) {

        $all = (bool)$rd->getParameter('all', false);

        $invisible = (bool)$rd->getParameter('invisible', false);

        $categories = $this->categories->getCategories($all, $invisible);

        $this->setAttributeByRef('categories', $categories);

        return $this->getDefaultViewName();
    }

    public function executeWrite(AgaviParameterHolder $rd) {

        if ($rd->getParameter('xaction', false) == 'create' 
            || $rd->getParameter('xaction', false) == 'update'
            || $rd->getParameter('xaction', false) == 'destroy') {

            $rows = json_decode($rd->getParameter('rows', array()));

            if (!is_array($rows)) {
                $rows = array($rows);
            }

            $c = array();

            foreach($rows as $category) {
                try {
                    if ($rd->getParameter('xaction', false) == 'destroy') {
                        if (isset($category->catid)) {
                            $this->categories->deleteCategoryRecord($category->catid);
                        }
                    } else {
                        $this->categories->createCategory((array)$category);
                        $c[] = (array)$category;
                    }
                } catch (Doctrine_Exception $e) {}
            }

            $this->setAttributeByRef('categories', $c);

            return $this->getDefaultViewName();

        }

        return $this->executeRead($rd);
    }

    public function isSecure() {
        return true;
    }

    public function getCredentials() {
        return array('icinga.user');
    }

    public function handleError(AgaviRequestDataHolder $rd) {
        return $this->getDefaultViewName();
    }
}

?>