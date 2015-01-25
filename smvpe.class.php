<?php

/**
 * SMVPE - Social Media Video Parse & Embed
 *
 * This class allows you to pass a source url from
 * a number of social media video wesbites and parse
 * it in order to embed the video associated with it.
 *
 * @author  Len Bradley <lenbradley@ninesphere.com>
 * @license http://www.php.net/license/3_01.txt PHP License 3.01
 * @version 0.1.0
 */

class SMVPE
{
    public $source, $site, $options;

    public function __construct( $source = '', $options = array() )
    {        
        $this->setSource( $source );
        $this->setOptions( $options );
    }    

    public function setOptions( $options = array() )
    {
        $defaults = array(
            'height'    => '480',
            'width'     => '860',
            'echo'      => false,
            'container' => '<div class="video">%1$s</div>',
            'class'     => '',
            'id'        => '',
            'params'    => null
        );

        $this->options = array_merge( $defaults, $options );
    }

    public function setSource( $source = '' )
    {
        $this->source   = $this->validateURL( $source );
        $this->site     = $this->getSourceProvider( $this->source );
    }

    public function getSites()
    {
        $sites = array(
            'break' => array(
                'url'   => 'http://www.break.com',
                'embed' => '//www.break.com/embed/{id}',
                'data'  => '',
                'regex' => ''
            ),
            'dailymotion' => array(
                'url'   => 'http://www.dailymotion.com',
                'embed' => '//www.dailymotion.com/embed/video/{id}',
                'data'  => '//api.dailymotion.com/video/{id}',
                'regex' => ''
            ),
            'metacafe' => array(
                'url'   => 'http://www.metacafe.com',
                'embed' => '//www.metacafe.com/embed/{id}/',
                'data'  => '//www.metacafe.com/api/item/{id}/',
                'regex' => ''
            ),
            'vimeo' => array(
                'url'   => 'https://vimeo.com/',
                'embed' => '//player.vimeo.com/video/{id}',
                'data'  => '//vimeo.com/api/v2/video/{id}.json',
                'regex' => ''
            ),
            'youtube' => array(
                'url'   => 'https://www.youtube.com/',
                'embed' => '//www.youtube.com/embed/{id}',
                'data'  => '//gdata.youtube.com/feeds/api/videos/{id}',
                'regex' => '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x'
            )
        );

        return $sites;
    }

    public function validateURL( $url = '' )
    {
        if ( strpos( $url, '<' ) === false && strpos( $url, '>' ) === false ) {

            $valid_url = false;

            foreach ( array( 'http://', 'https://', '//' ) as $check ) {
                if ( strtolower( substr( $url, 0, strlen( $check ) ) ) == $check ) {
                    $valid_url = true;
                }
            }

            if ( $valid_url === false ) {
                $url = '//' . $url;
            }
        }

        return $url;
    }

    public function getSourceProvider( $source = null )
    {
        $source = ( $source === null ) ? $this->source : $source;
        $source = str_replace( '.', '', $source );

        foreach ( $this->getSites() as $site => $data ) {
            if ( strpos( $source, $site ) !== false ) {
                return $site;
            }
        }

        return false;
    }

    public function parseParameters( $params = null )
    {
        if ( $params === null ) {
            $params = $this->options['params'];
        }

        if ( is_string( $params ) ) {
            $params = '?' . ltrim( $params, '?' );
        }

        if ( is_array( $params ) && ! empty( $params ) ) {
            $params = '?' . http_build_query( $params );
        }        

        return $params;
    }

    public function extractID( $source = null, $site = null )
    {
        $source = ( $source === null ) ? $this->source : $source;
        $site   = ( $site === null ) ? $this->getSourceProvider( $source ) : $site;

        switch ( $site ) {
            case 'break' :

                break;
            case 'dailymotion' :

                break;
            case 'metacafe' :

                break;
            case 'vimeo' :

                break;
            case 'youtube' :

                break;
            default :
                return false;
        }
    }

    protected function insertID( $subject = '', $source = null, $site = null )
    {
        $source = ( $source === null ) ? $this->source : $source;
        $site   = ( $site === null ) ? $this->getSourceProvider( $source ) : $site;

        $sites  = $this->getSites();
        $id     = $this->extractID( $source, $site );

        if ( $id !== false ) {
            return str_replace( '{id}', $string, $subject );
        } else {
            return false;
        }
    }

    public function embed( $source = null, $site = null )
    {
        $source = ( $source === null ) ? $this->source : $source;
        $site   = ( $site === null ) ? $this->getSourceProvider( $source ) : $site;

        $output = sprintf( $this->options['container'], $output );
    }
}

?>