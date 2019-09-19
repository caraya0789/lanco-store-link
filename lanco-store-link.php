<?php
/**
 * Plugin Name:     Lanco Store Link
 * Plugin URI:      http://lancopaints.com
 * Description:     Shows a CTA Link in the homepage for Costa Rica Users
 * Author:          Cristian Araya J.
 * Author URI:      http://codeskill.io
 * Text Domain:     lanco-store-link
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Lanco_Store_Link
 */
define('LSTOREL_PATH', __DIR__);
define('LSTOREL_VERSION', '0.1.0');

require LSTOREL_PATH . '/vendor/autoload.php';

class Lanco_Store_Link {

    protected static $instance;

    public static function get_instance() {
        if(null === self::$instance)
            self::$instance = new self();

        return self::$instance;
    }

    public function is_from_cr() {
        if(!empty($_COOKIE['lstorel_cr']))
            return true;

        if(empty($_SERVER['REMOTE_ADDR']))
            return false;

        $gi = geoip_open( LSTOREL_PATH . '/data/GeoLiteCity.dat', GEOIP_STANDARD );
        $row = GeoIP_record_by_addr($gi, '200.105.99.14'); //$_SERVER['REMOTE_ADDR']);

        if(null === $row)
            return false;

        if($row->country_code == 'CR') {
            setcookie('lstorel_cr', 1, time() + 86400);
            return true;
        }
    }

    public function hooks() {
        if(!$this->is_from_cr())
            return;
        
        add_action( 'wp_footer', [$this, 'markup'] );
        add_action( 'wp_head', [$this, 'styles'] );
    }

    public function markup() {
        ?>
        <a target="_blank" class="lstorel-cta" href="https://www.lancostore.com">Comprar en Línea</a>
        <?php
    }

    public function styles() {
        ?>
        <style>
        .lstorel-cta {
            position: fixed;
            top: 500px;
            right: 0px;
            background: #de3838;
            color: #fff;
            display: block;
            width: 200px;
            line-height: 38px;
            font-weight: bold;
            padding: 0 20px;
            font-size: 16px;
            z-index: 20;
            text-transform: uppercase;
            text-align: right;
        }
        .lstorel-cta:hover,
        .lstorel-cta:active {
            color: #fff;
            text-decoration: none;
            box-shadow: 0px 0px 10px 2px rgba(222, 56, 56, 0.4);
        }
        @media(max-width:768px) {
            .lstorel-cta {
                top:auto;
                bottom:0;
                width:auto;
                left:0;
                text-align:center;
            }
        }
        </style>
        <?php
    }



}

function lstorel_get_instance() {
	return Lanco_Store_Link::get_instance();
}

add_action( 'plugins_loaded', [ lstorel_get_instance(), 'hooks' ] );
