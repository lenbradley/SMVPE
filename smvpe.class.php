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
 * @version 0.3.0
 */

class SMVPE
{
    public $source, $site, $options;

    public function __construct( $source = '', $options = array() )
    {        
        $this->setSource( $source );
        $this->setOptions( $options );
    }

    public static function init( $source = '', $options = array() )
    {
        return new SMVPE( $source, $options );
    }

    public function setOptions( $options = array() )
    {
        $defaults = array(
            'height'    => '480',
            'width'     => '860',
            'container' => '<div class="video">%1$s</div>',
            'params'    => null
        );

        $this->options = array_merge( $defaults, $options );
    }

    public function setOption( $name = '', $value = '' )
    {
        if ( trim( $name ) != '' ) {
            $this->options[$name] == $value;
        }

        return $this;
    }

    public function setSource( $source = '', $options = array() )
    {
        $this->source   = $this->validateURL( $source );
        $this->site     = $this->getSourceProvider( $this->source );

        if ( ! empty( $options ) ) {
            foreach ( $options as $name => $value ) {
                $this->setOption( $name, $params );
            }
        }

        return $this;
    }

    public function setParameters( $params = '' )
    {
        $this->options['params'] = $params;
        return $this;
    }

    public function getSites()
    {
        $sites = array(
            'break' => array(
                'url'   => 'http://www.break.com',
                'embed' => '//www.break.com/embed/{id}',
                'data'  => ''
            ),
            'dailymotion' => array(
                'url'   => 'http://www.dailymotion.com',
                'embed' => '//www.dailymotion.com/embed/video/{id}',
                'data'  => ''
            ),
            'metacafe' => array(
                'url'   => 'http://www.metacafe.com',
                'embed' => '//www.metacafe.com/embed/{id}/',
                'data'  => '//www.metacafe.com/api/item/{id}/'
            ),
            'vimeo' => array(
                'url'   => 'https://vimeo.com/',
                'embed' => '//player.vimeo.com/video/{id}',
                'data'  => '//vimeo.com/api/v2/video/{id}.json'
            ),
            'youtube' => array(
                'url'   => 'https://www.youtube.com/',
                'embed' => '//www.youtube.com/embed/{id}',
                'data'  => '//gdata.youtube.com/feeds/api/videos/{id}'
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

    public function getParametersFromSource( $source = null )
    {
        $source = ( $source === null ) ? $this->source : $source;
        $source = parse_url( $source );        

        if ( isset( $source['query'] ) ) {
            return $source['query'];
        }

        return '';
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
        $parsed = parse_url( $source );

        if ( isset( $parsed['path'] ) ) {
            $parsed['path'] = trim( $parsed['path'], '/' );
        }

        switch ( $site ) {
            case 'break' :
                if ( isset( $parsed['path'] ) ) {
                    $parsed['path'] = explode( '/', $parsed['path'] );
                    $parsed['path'] = array_pop( $parsed['path'] );
                    $parsed['path'] = explode( '-', $parsed['path'] );
                    $parsed['path'] = array_pop( $parsed['path'] );

                    return $parsed['path'];
                }

                break;
            case 'dailymotion' :
                if ( isset( $parsed['path'] ) ) {
                    $parsed['path'] = explode( '/', $parsed['path'] );
                    $parsed['path'] = array_pop( $parsed['path'] );

                    if ( strpos( $parsed['path'], '_' ) !== false ) {
                        $parsed['path'] = explode( '_', $parsed['path'] );
                        $parsed['path'] = $parsed['path'][0];
                    }

                    return $parsed['path'];
                }

                break;
            case 'metacafe' :
                if ( isset( $parsed['path'] ) ) {
                    $parsed['path'] = explode( '/', $parsed['path'] );

                    if ( isset( $parsed['path'][1] ) ) {
                        return $parsed['path'][1];
                    }
                }

                break;
            case 'vimeo' :
                if ( isset( $parsed['path'] ) ) {
                    $parsed['path'] = explode( '/', $parsed['path'] );
                    $parsed['path'] = array_pop( $parsed['path'] );

                    return $parsed['path'];
                }

                break;
            case 'youtube' :
                if ( isset( $parsed['query'] ) ) {

                   parse_str( $parsed['query'], $query );

                    if ( isset( $query['v'] ) ) {
                        return $query['v'];
                    }
                }

                if ( isset( $parsed['path'] ) ) {
                    $parsed['path'] = explode( '/', $parsed['path'] );
                    $parsed['path'] = array_pop( $parsed['path'] );

                    return $parsed['path'];
                }
                break;
            default :
                return 0;
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

    public function getEmbedCode( $source = null, $site = null )
    {
        $source = ( $source === null ) ? $this->source : $source;
        $site   = ( $site === null ) ? $this->getSourceProvider( $source ) : $site;
        $sites  = $this->getSites();
        $output = '';

        if ( isset( $sites[$site] ) && $video_id = $this->extractID() ) {
            $embed_url  = str_replace( '{id}', $video_id, $sites[$site]['embed'] );
            $output     = '<iframe src="' . $embed_url . $this->parseParameters() . '" width="' . $this->options['width'] . '" height="' . $this->options['height'] . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
            $output     = sprintf( $this->options['container'], $output );
        }

        return $output;
    }

    public function embed( $source = null, $site = null )
    {
        $source = ( $source === null ) ? $this->source : $source;
        $site   = ( $site === null ) ? $this->getSourceProvider( $source ) : $site;

        echo $this->getEmbedCode( $source, $site );
    }
}

?>