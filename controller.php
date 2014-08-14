<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class CoreCommerceEwayPackage extends Package {

    protected $pkgHandle = 'core_commerce_eway';
    protected $appVersionRequired = '5.3.0';
    protected $pkgVersion = '0.6';

    public function getPackageDescription() {
        return t("E-Way Payment Gateway for Core Commerce");
    }

    public function getPackageName() {
        return t("E-Way Payment Gateway");
    }

    public function getPackageHandle() {
        return $this->pkgHandle;
    }

    public function install() {
        $pkg = parent::install();
        Loader::model('payment/method', 'core_commerce');
        CoreCommercePaymentMethod::add('eway', 'E-Way Payment Gateway', false, $pkg);
    }

}
