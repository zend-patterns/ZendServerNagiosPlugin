<?php
$zf2Path = get_cfg_var('zf2_path'); //Should retrun the Zend Server ZF2 library path.
if ($zf2Path) {
	include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
	Zend\Loader\AutoloaderFactory::factory(array(
		'Zend\Loader\StandardAutoloader' => array(
			'autoregister_zf' => true,
		)
	));
}
if (!class_exists('Zend\Loader\AutoloaderFactory')) {
	throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
}
