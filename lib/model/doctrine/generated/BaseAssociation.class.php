<?php
/**
 * $Id$
 */
class BaseAssociation extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_documents_associations');

        $this->hasColumn('main_id', 'integer', 10, array('primary' => true));
        $this->hasColumn('linked_id', 'integer', 10, array('primary' => true));
        $this->hasColumn('type', 'string', 2); // sr, ro ... no need to declare that it is a foreign key.
    }
    
    public function setUp()
    {
        $this->hasOne('Document as Main', 'Association.main_id');
        $this->hasOne('Document as Linked', 'Association.linked_id');
        // dangerous but more performant than fetching on documents view for 'sr' association type:
        $this->hasMany('Association as MainAssociation', array('local' => 'main_id', 'foreign' => 'linked_id'));
        $this->hasMany('Association as LinkedAssociation', array('local' => 'linked_id', 'foreign' => 'main_id'));
        $this->hasMany('Association as MainMainAssociation', array('local' => 'linked_id', 'foreign' => 'linked_id'));
        $this->hasMany('Association as LinkedLinkedAssociation', array('local' => 'main_id', 'foreign' => 'main_id'));
        $this->hasMany('GeoAssociation as MainGeoassociations', array('local' => 'main_id', 'foreign' => 'main_id')); 
        $this->hasMany('GeoAssociation as LinkedGeoassociations', array('local' => 'linked_id', 'foreign' => 'main_id')); 
        $this->hasMany('Document as MainDocument', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Document as LinkedDocument', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('Summit as Summit', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Summit as LinkedSummit', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('SummitI18n as SummitI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('SummitI18n as LinkedSummitI18n', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('Route as Route', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Route as LinkedRoute', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('RouteI18n as RouteI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('RouteI18n as LinkedRouteI18n', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('Parking as Parking', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Parking as LinkedParking', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('ParkingI18n as ParkingI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('ParkingI18n as LinkedParkingI18n', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('Hut as Hut', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Hut as LinkedHut', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('HutI18n as HutI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('HutI18n as LinkedHutI18n', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('Site as Site', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Site as LinkedSite', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('SiteI18n as SiteI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('SiteI18n as LinkedSiteI18n', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('Article as Article', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Outing as Outing', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Outing as LinkedOuting', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('OutingI18n as OutingI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('OutingI18n as LinkedOutingI18n', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('User as User', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('User as LinkedUser', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('UserI18n as UserI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('UserI18n as LinkedUserI18n', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('UserPrivateData as UserPrivateData', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('UserPrivateData as LinkedUserPrivateData', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('Product as Product', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Product as LinkedProduct', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('ProductI18n as ProductI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('ProductI18n as LinkedProductI18n', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('Image as Image', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('ImageI18n as ImageI18n', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('Book as Book', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('BookI18n as BookI18n', array('local' => 'main_id', 'foreign' => 'id'));
    }
}
