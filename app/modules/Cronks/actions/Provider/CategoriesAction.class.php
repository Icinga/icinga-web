<?php

class Cronks_Provider_CategoriesAction extends CronksBaseAction {

    /**
     * @var Cronks_Provider_CronksDataModel
     */
    private $cronks = null;

    /**
     * @var NsmUser
     */
    private $user = null;

    public function initialize(AgaviExecutionContainer $container) {
        parent::initialize($container);

        $this->user = $this->getContext()->getUser()->getNsmUser();

        $this->cronks = $this->getContext()->getModel('Provider.CronksData', 'Cronks');
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

        $categories = $this->cronks->getCategories($all, $invisible);

        $this->setAttributeByRef('categories', $categories);

        return $this->getDefaultViewName();
    }

    public function executeWrite(AgaviParameterHolder $rd) {

        if ($rd->getParameter('xaction', false) == 'create' || $rd->getParameter('xaction', false) == 'update' || $rd->getParameter('xaction', false) == 'destroy') {

            $rows = json_decode($rd->getParameter('rows', array()));

            if (!is_array($rows)) {
                $rows = array($rows);
            }

            $c = array();

            foreach($rows as $category) {
                try {
                    if ($rd->getParameter('xaction', false) == 'destroy') {
                        if (isset($category->catid)) {
                            $this->cronks->deleteCategoryRecord($category->catid);
                        }
                    } else {
                        $this->cronks->createCategory((array)$category);
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