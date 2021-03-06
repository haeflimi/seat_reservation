<?php
namespace Concrete\Package\SeatMap;

use Package,
    Concrete\Core\Backup\ContentImporter,
    Route,
    Core,
    Config,
    Events;

class Controller extends Package
{
    protected $pkgHandle = 'seat_map';
    protected $appVersionRequired = '5.7.4';
    protected $pkgVersion = '0.9';

    public function getPackageName()
    {
        return t('Seat Map');
    }

    public function getPackageDescription()
    {
        return t('Concrete5 Block that displays a Seatmap based on a svg vector graphic and allows your community users to pick/ reserve seats.');
    }

    public function on_start()
    {
        Route::registerMultiple(array(
            '/ccm/seat_map/claim_seat/{action}' => array('\Concrete\Package\SeatMap\Block\SeatMap\Controller::claim_seat')
        ));
    }

    public function install()
    {
        $pkg = parent::install();
        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/install.xml');
    }

    public function upgrade()
    {
        parent::upgrade();
    }

    public function uninstall(){

    }
}