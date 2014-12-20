<?php

namespace Timely\Ai1ec\Skeleton\Documentation;

if ( 'cli' !== php_sapi_name() ) {
    return;
}

$base_dir     = dirname( __DIR__ );

$documentable = new \RegexIterator(
	new Ai1ecsaBuilderFilter(
        new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $base_dir,
                \FilesystemIterator::SKIP_DOTS
            )
        ),
		$base_dir
	),
	'/^.+\.php$/i',
	\RegexIterator::GET_MATCH
);

foreach ( $documentable as $entry ) {
	$php_file = $entry[0];
	$md_file  = \dirname( $php_file ) . DIRECTORY_SEPARATOR .
		\basename( $php_file, '.php' ) . '.md';
	\file_put_contents(
		$md_file,
		Generator::parse( $php_file )
	);
}

class Generator {

	protected $file;

	static public function parse( $file ) {
		$parser = new self( $file );
		return $parser->extract_md();
	}

	public function __construct( $file ) {
		if ( ! is_file( $file ) || ! is_readable( $file ) ) {
			throw new InvalidArgumentException(
				'File \'' . $file . '\' is unreadable'
			);
		}
		$this->file = $file;
	}

	public function extract_md() {
		$tokens  = token_get_all( file_get_contents( $this->file ) );
        $digest  = '';
        $content = 0;
        $code    = '';
        foreach ( $tokens as $token ) {
            if ( $content > 0 ) {
                $c_line = isset( $token[2] ) ? $token[2] : $content;
                if ( $content >= $c_line ) {
                    if (
                        is_array( $token ) && (
                            (
                                T_WHITESPACE === $token[0] &&
                                substr_count( $token[1], "\n" ) > 1
                            ) || (
                                T_COMMENT === $token[0]
                            )
                        )
                    ) {
                        $content = 0;
                    }
                    if ( $content === $c_line ) {
                        $code .= ( isset( $token[1] ) ) ? $token[1] : $token;
                    }
                } else {
                    if ( strlen( $code ) > 5 ) {
                        $digest .= sprintf(
                            "```php\n%s\n```\n",
                            trim( $code )
                        );
                        $code = '';
                    }
                    $content = 0;
                }
            } elseif ( is_array( $token ) && T_DOC_COMMENT === $token[0] && false === strpos( $token[1], 'Plugin URI' ) ) {
                $lines = preg_split( '|[\r\n]+|', $token[1] );
                $chunk = $this->to_digest( $lines );
                if ( $chunk ) {
                    $digest .= $chunk;
                    $content = $token[2] + count( $lines );
                }
            }
        }
        return trim( $digest );
	}

    public function to_digest( array $lines ) {
        // @TODO: use ai1ec.registry to link to classes mentioned in '@param' and '@return'
        $lines = array_slice( $lines, 1, -1 );
        $lines = array_map(
            [ $this, 'clean_line' ],
            $lines
        );
        $result  = '';
        $index   = 0;
        while ( isset( $lines[$index] ) ) {
            if (
                preg_match( '|^[=\-]{4,}$|', $lines[$index] ) &&
                isset( $lines[$index + 2] )
            ) {
                $title = implode(
                    ' ',
                    array_map(
                        function( $word ) {
                            return ucfirst( strtolower( $word ) );
                        },
                        explode( ' ', $lines[$index + 1] )
                    )
                );
                $result .= "\n" . $title . "\n" .
                    str_repeat( $lines[$index]{0}, strlen( $title ) ) .
                    "\n";
                $index += 2;
            } else {
                $result .= $lines[$index] . "\n";
            }
            ++$index;
        }
        return $result;
    }

    public function clean_line( $line ) {
        return trim( substr( $line, 3 ) );
    }

}

class Ai1ecsaBuilderFilter extends \FilterIterator {

	protected $base_path;

	public function __construct( \Iterator $rit, $path ) {
        $this->base_path   = $path;
        $this->base_substr = strlen( $path );
		parent::__construct( $rit );
    }

	public function accept() {
		if (
            0 === strpos( $this->current()->getPathname(), $this->base_path . '/doc' ) ||
            0 === strpos( $this->current()->getPathname(), $this->base_path . '/.git' )
        ) {
			return false;
		}
		return true;
	}

}