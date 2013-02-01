<?php
/**
 * parkings module actions.
 *
 * @package    c2corg
 * @subpackage parkings
 * @version    $Id: actions.class.php 1132 2007-08-01 14:38:06Z fvanderbiest $
 */
class parkingsActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Parking';

    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();
        
        if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
        {
            $user = $this->getUser();
            $prefered_cultures = $user->getCulturesForDocuments();
            $current_doc_id = $this->getRequestParameter('id');
            
            $main_associated_parkings = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_parking')), 'elevation');
            
            $parking_ids = array();
            if (count($main_associated_parkings))
            {
                $associated_parkings = Association::addChildWithBestName($main_associated_parkings, $prefered_cultures, 'pp', $current_doc_id, true);
                $associated_parkings = Parking::getAssociatedParkingsData($associated_parkings);
                
                if (count($main_associated_parkings) > 1 || count($associated_parkings) == 1)
                {
                    foreach ($main_associated_parkings as $parking)
                    {
                        $parking_ids[] = $parking['id'];
                    }
                }
                
                if (count($parking_ids))
                {
                    $associated_parking_routes = Association::findWithBestName($parking_ids, $prefered_cultures, array('pr', 'ph', 'pf'));
                    $this->associated_docs = array_merge($this->associated_docs, $associated_parking_routes);
                }
            }
            else
            {
                $associated_parkings = $main_associated_parkings;
            }
            
            $this->associated_parkings = $associated_parkings;
            
            array_unshift($parking_ids, $current_doc_id);
            $this->ids = implode('-', $parking_ids);
            
            $associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ');
            $this->associated_routes = $associated_routes;
            
            $route_ids = array();
            $associated_routes_books = array();
            if (count($associated_routes))
            {
                foreach ($associated_routes as $route)
                {
                    if ($route['duration'] instanceof Doctrine_Null || $route['duration'] <= 4)
                    {
                        $route_ids[] = $route['id'];
                    }
                }
                
                if (count($route_ids))
                {
                    $associated_route_docs = Association::findWithBestName($route_ids, $prefered_cultures, array('br'), false, false);
                    if (count($associated_route_docs))
                    {
                        $associated_route_docs = c2cTools::sortArray($associated_route_docs, 'name');
                        $associated_routes_books = array_filter($associated_route_docs, array('c2cTools', 'is_book'));
                        foreach ($associated_routes_books as $key => $book)
                        {
                            $associated_routes_books[$key]['parent_id'] = true;
                        }
                    }
                }
            }

            $cab = 0;
            if (count($associated_routes_books))
            {
                $associated_books = Book::getAssociatedBooksData($associated_routes_books);
                $this->associated_books = $associated_books;
                $cab = count($associated_books);
            }
            
            // get associated outings
            $latest_outings = array();
            $nb_outings = 0;
            if (count($associated_routes))
            {
                $outing_params = array('parkings' => $this->ids);
                $nb_outings = sfConfig::get('app_nb_linked_outings_docs');
                $latest_outings = Outing::listLatest($nb_outings + 1, array(), array(), array(), $outing_params, false);
                $latest_outings = Language::getTheBest($latest_outings, 'Outing');
            }
            $this->latest_outings = $latest_outings;
            $this->nb_outings = $nb_outings;
            
            $this->associated_huts = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_hut')), 'elevation');
            
            $this->associated_products = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_product')), 'name');
            
            $related_portals = array();
            $public_transportation_rating = $this->document->get('public_transportation_rating');
            if (in_array($public_transportation_rating, array(1, 2, 4, 5)))
            {
                $related_portals[] = 'cda';
            }
            Portal::getRelatedPortals($related_portals, $this->associated_areas, $associated_routes);
            $this->related_portals = $related_portals;
            
            $this->section_list = array('books' => ($cab != 0), 'map' => (boolean)$this->document->get('geom_wkt'));
    
            $description = array($this->__('parking') . ' :: ' . $this->document->get('name'),
                                 $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    public function executePopup()
    {
        parent::executePopup();
        $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ', $this->document->get('id'));
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        $this->addAroundParam($out, 'parnd');
        $this->addNameParam($out, 'pnam');
        $this->addCompareParam($out, 'palt');
        $this->addListParam($out, 'tp');
        $this->addListParam($out, 'tpty');
        $this->addParam($out, 'geom');
        $this->addParam($out, 'pcult');
        
        return $out;
    }

    /**
     * Executes list action
     */
    public function executeList()
    {
        parent::executeList();

        $nb_results = $this->nb_results;
        if ($nb_results == 0) return;

        $timer = new sfTimer();
        $parkings = $this->query->execute(array(), Doctrine::FETCH_ARRAY);
        c2cActions::statsdTiming('pager.getResults', $timer->getElapsedTime());
        
        $timer = new sfTimer();
        Document::countAssociatedDocuments($parkings, 'pr', true);
        c2cActions::statsdTiming('document.countAssociatedDocuments', $timer->getElapsedTime());

        $this->items = Language::parseListItems($parkings, 'Parking');
    }
}
