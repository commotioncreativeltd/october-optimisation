<?php namespace Commotion\Optimisation;

use Illuminate\Support\Facades\App;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

	/**
	 * plugin boot method
	 * 
	 */
    public function boot()
    {

    	// minify the final output
    	$this->minifyHTML();
        
    }

    /**
     * minify the final HTML output
     */
    private function minifyHTML()
    {
    	App::after(function($request, $response)
        {
            if(App::Environment() != 'local')
            {
                if(is_a($response, 'Illuminate\Http\Response'))
                {
                    $buffer = $response->getContent();
                    if(strpos($buffer,'<pre>') !== false)
                    {
                        $replace = array(
                            '/<!--[^\[](.*?)[^\]]-->/s' => '',
                            "/<\?php/"                  => '<?php ',
                            "/\r/"                      => '',
                            "/>\n</"                    => '><',
                            "/>\s+\n</"                 => '><',
                            "/>\n\s+</"                 => '><',
                        );
                    }
                    else
                    {
                        $replace = array(
                            '/<!--[^\[](.*?)[^\]]-->/s' => '',
                            "/<\?php/"                  => '<?php ',
                            "/\n([\S])/"                => '$1',
                            "/\r/"                      => '',
                            "/\n/"                      => '',
                            "/\t/"                      => '',
                            "/ +/"                      => ' ',
                        );
                    }
                    $buffer = preg_replace(array_keys($replace), array_values($replace), $buffer);
                    $response->setContent($buffer);
                }
            }
        });
    }

}
