<?php
namespace Concrete\Package\SeatMap\Block\SeatMap;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\User\Group\GroupList;
use File;
use Package;
use Concrete\Core\Attribute\Key\UserKey;
use Concrete\Core\Attribute\StandardSetManager;
use Concrete\Core\Attribute\SetFactory;
use UserList;
use User;

class Controller extends BlockController
{
    protected $btTable = 'btSeatMap';
    protected $btInterfaceWidth = "1024";
    protected $btInterfaceHeight = "768";
    protected $btCacheBlockRecord = false;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputLifetime = CACHE_LIFETIME;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;

    public function getBlockTypeName()
    {
        return t('Seat Map');
    }

    public function getBlockTypeDescription()
    {
        return t('Concrete5 Block that displays a Seatmap based on a svg vector graphic and allows your community users to pick/ reserve seats.');
    }

    public function add()
    {
        $this->prepFormData();
    }

    public function edit()
    {
        $this->prepFormData();
    }

    public function save($args)
    {
        $pkg = Package::getByHandle('seat_map');
        $em = \ORM::entityManager();
        parent::save($args);
        if($args['class']){
            $service = $this->app->make('Concrete\Core\Attribute\Category\CategoryService');
            $categoryEntity = $service->getByHandle('user');
            $category = $categoryEntity->getController();
            $akHandle = $args['class'].'_reservation';
            $ak = $category->getByHandle($akHandle);
            $setHandle = 'seat_map_reservations';
            $sf = new SetFactory($em);
            $sm = new StandardSetManager($categoryEntity, $em);
            $set = $sf->getByHandle($setHandle);
            if(!is_object($set)){
                $sm->addSet($setHandle, t('Seat Map Reservations'), $pkg);
                $set = $sf->getByHandle($setHandle);
            }
            if(!is_object($ak)){
                $ak = new UserKey();
                $ak->setAttributeKeyHandle($akHandle);
                $ak->setAttributeKeyName(t('Seat Reservation Attribute for Map width Class: "%s"',$akHandle));
                $ak = $category->add('text', $ak, null, $pkg);
                $sm->addKey($set, $ak);
            }
        }
    }

    public function view()
    {
        $this->requireAsset('javascript', 'bootstrap/tooltip');
        $this->requireAsset('javascript', 'bootstrap/popover');
        // Load the SVG File content
        $f = File::getByID($this->fID);
        $svgMap = $f->getFileContents();
        $reservations = array();
        // Check if the Attribute for the map exists
        if(!empty(UserKey::getByHandle($this->class.'_reservation'))){
            // Filter the User List for that attribute
            $ul = new UserList;
            $ul->filterByAttribute($this->class.'_reservation', '', '!=');
            foreach($ul->getResults() as $u){
                $reservations[$u->getAttribute($this->class.'_reservation')] = $u;
            }
            // Check the current User for that attribute to determine users seat
            $u = new User();
            if($u->isLoggedIn()){
                $ui = $u->getUserInfoObject();
                $mySeat = $ui->getAttribute($this->class.'_reservation');
                $this->set('mySeat', $mySeat);
            }
        }
        $this->set('reservations', $reservations);
        $this->set('svgMap', $svgMap);
        $this->set('class', $this->class);
        $this->set('fID', $this->fID);
        $this->set('gID', $this->gID);
        $this->set('bID', $this->bID);
    }

    private function prepFormData()
    {
        $gl = new GroupList;
        $allGroups = [];
        $allGroups[0] = t('All');
        foreach($gl->getResults() as $group){
            $allGroups[$group->getGroupID()] = $group->getGroupPath();
        }
        $this->set('allGroups', $allGroups);
        $this->set('gID', $this->gID);
        $this->set('fID', $this->fID);
        $this->set('class', $this->class);
    }
}