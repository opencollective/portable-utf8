# Changelog

### 5.4.27 (2019-11-11)

- "UTF8::clean() / UTF8::cleanup()" -> do not remove invisible urlencoded strings by default

      -> problem with e.g. pdf content: "%1b;" -> ...p(CRnAYOD*9a1>VAk^mH%1b;?ZVuX$`P[%...

### 5.4.26 (2019-11-05)

- disable "Bootup::filterRequestUri()" && "Bootup::filterRequestInputs()" by default
      
      Since version 5.4.26 this library will NOT force "UTF-8" by "bootstrap.php" anymore.
      If you need to enable this behavior you can define "PORTABLE_UTF8__ENABLE_AUTO_FILTER", 
      before requiring the autoloader.
      
      ```
      define('PORTABLE_UTF8__ENABLE_AUTO_FILTER', 1);
      ```
      
      Before version 5.4.26 this behavior was enabled by default and you could disable it via "PORTABLE_UTF8__DISABLE_AUTO_FILTER", but the code had potential security vulnerabilities via injecting code while redirecting via ```header('Location ...```.
      This is the reason I decided to add this BC in a bug fix release, so that everybody using the current version will receive the security-fix.

### 5.4.25 (2019-10-14)

- update vendor (ASCII) lib

### 5.4.24 (2019-10-06)

- improve performance from "UTF8::UTF8::str_titleize_for_humans()" (use "array_merge" only if needed)
- improve performance from "UTF8::ucwords()" + "UTF8::lcwords()" (don't use "implode()" if it's not needed)

### 5.4.23 (2019-09-27)

- improve performance from "UTF8::chr_to_decimal()" (now we use iconv if it's available)
- improve performance from "UTF8::html_entity_decode()" (code cleanup, remove dead code)

### 5.4.22 (2019-09-26)

- improve performance by replacing ```count($a) === 0``` with ```$a === []```
   -> so we don't need a function call to check if an array is empty

### 5.4.21 (2019-09-17)

- improve performance by not using return types for ```private``` methods
   -> the code is already checked via phpstan + psalm + phpstorm, ...
      so no need to check it every time at runtime at this point

### 5.4.20 (2019-09-16)

- fix "preg_quote()" usage
- fix return type from "mb_encode_numericentity" & "mb_decode_numeric_entity" usage
- add "@deprecated" for all alias methods

### 5.4.19 (2019-09-05)

- move ASCII functions into a separated package "Portable ASCII"

### 5.4.18 (2019-08-21)

- optimize "UTF8::str_titleize()" + clean-up

### 5.4.17 (2019-08-21)

- fix "UTF8::get_file_type()" -> do not add too simple comparisons, because of false-positive results
- extend "UTF8::str_titleize()" -> allow to add "word"-chars as new parameter

### 5.4.16 (2019-08-15)

- optimize "UTF8::str_detect_encoding()"

### 5.4.15 (2019-08-06)

- extend "UTF8::range()" -> support for different steps
- fix "UTF8::str_detect_encoding()" detecting of "UTF-32"
- revert: use "mb_detect_order()" for "UTF8::str_detect_encoding()"

### 5.4.14 (2019-08-02)

- extend "UTF8::range()" -> support for e.g. "a-z" + different encoding

### 5.4.13 (2019-08-01)

- extend "UTF8::wordwrap_per_line()" -> split the input by "$delimiter"

### 5.4.12 (2019-07-31)

- fix "UTF8::wordwrap_per_line()" -> use unicode version of "wordwrap"

### 5.4.11 (2019-07-19)

- use "mb_detect_order()" for "UTF8::str_detect_encoding()"
- code clean-up + optimize regex usage

### 5.4.10 (2019-07-05)

- fix "UTF8::str_contains_any()" -> thanks @drupalista-br

### 5.4.9 (2019-06-19)

- sync with "PHP RFC: Add str begin and end functions"
- fix "UTF8::rawurldecode()" and "UTF8::urldecode()" -> for non multi usage

### 5.4.8 (2019-06-08)

- fix typo in "win1252_to_utf8.php"
- optimize "UTF8::fix_simple_utf8()"

### 5.4.7 (2019-05-28)

- optimize performance for "bootstrap.php"

### 5.4.6 (2019-04-25)

- fix "UTF8::to_latin1()" for non-char input + optimize performance

### 5.4.5 (2019-04-21)

- fix unicode support for regex 

### 5.4.4 (2019-04-15)

- optimize performance for UTF8::rawurldecode() and UTF8::urldecode()
- optimize "UTF8::str_split_pattern()" with limit usage
- fix warnings detected by psalm && phpstan && phpstorm

### 5.4.3 (2019-03-05)

- optimize "UTF8::strrev()" with support for emoji chars
- added "UTF8::emoji_encode()" + "UTF8::emoji_decode()"

### 5.4.2 (2019-02-11)

- optimize html-encoding for unicode surrogate pairs (e.g. UTF-16)

### 5.4.1 (2019-02-10)

- optimize some RegEx
- fix html-encoding for unicode surrogate pairs (e.g. UTF-16)

### 5.4.0 (2019-01-22)
- optimize performance | thx @fe3dback
  -> e.g. use "\mb_"-functions without encoding parameter
  -> e.g. simplify logic of "UTF8::str_pad()"
- no more 100% support for "mbstring_func_overload", it's already deprecated in php
- move "UTF8::checkForSupport()" into "bootstrap.php"
- fix output from "UTF8::str_pad()" + empty input string
- add more "encoding" parameter e.g. for "UTF8::str_shuffle()"
- remove really old fallback for breaking-changes
- do not use aliases for internal processing

### 5.3.3 (2019-01-11)
- update "UTF8::is_json()" + tests

### 5.3.2 (2019-01-11)
- update "UTF8::is_base64()" + tests

### 5.3.1 (2019-01-11)
- update "UTF8::str_truncate_safe()" + tests

### 5.3.0 (2019-01-10)
- use autoloader + namespace for "tests/"
- fixes suggested by "phpstan" level 7
- fixes suggested by "psalm" 
- use variable references whenever possible
- use types for callback functions
- sync "UTF8::strcspn()" with native "strcspn()"
- sync "UTF8::strtr()" with native "strtr()"

### 5.2.16 (2019-01-02)
- update phpcs fixer config
- optimizing via "rector/rector"

### 5.2.15 (2018-12-18)
- optimize "getData()"
- use phpcs fixer

### 5.2.14 (2018-12-07)
- optimize "UTF8::str_replace_beginning()" && "UTF8::str_replace_ending()"
- added "UTF8::str_ireplace_beginning()" && "UTF8::str_ireplace_ending()"

### 5.2.13 (2018-11-29)
- "UTF8::get_file_type()" is now public + tested

### 5.2.12 (2018-11-29)
- optimize "UTF8::ord()" performance

### 5.2.11 (2018-10-19)
- merge UTF8::titlecase() && UTF8::str_titleize()
- add new langage + keep-string-length arguments for string functions

### 5.2.10 (2018-10-19)
- test with PHP 7.3

### 5.2.9 (2018-10-01)
- fix binary check for UTF16 / UTF32

### 5.2.8 (2018-09-29)
- "composer.json" -> remove extra alias
- UTF8::substr_replace() -> optimize performance
- UTF8::clean() -> add tests with "\00"
- update "UTF8::get_file_type()"
- fix fallback for "UTF8::encode()"

### 5.2.7 (2018-09-15)
- simplify "UTF8::encode()"

### 5.2.6 (2018-09-15)
- use more vanilla php fallbacks
- new encoding-from / -to parameter for "UTF8::encode()"
- optimize "mbstring_func_overload" fallbacks

### 5.2.5 (2018-09-11)
- more fixes for "mbstring_func_overload"

### 5.2.4 (2018-09-11)
- optimize performance for "UTF8::remove_bom()"
- optimize performance for "UTF8::is_binary()"
- fix tests with "mbstring_func_overload"

### 5.2.3 (2018-09-07)
- fix some breaking changes from "strict_types=1"

### 5.2.2 (2018-09-06)
- use "UTF8::encode()" internal ...

### 5.2.1 (2018-08-06)
- add more php-warnings
- optimize native php fallback
- fix tests without "mbstring"-ext
- UTF8::strlen() can return "false", if "mbstring" is not installed
    
### 5.2.0 (2018-08-05)
- use phpstan (+ fixed many code smells)
- added more tests

### 5.1.0 (2018-08-03)
- merge methods from "Stringy" into "UTF8"
- added many new tests

### 5.0.6 (2018-05-02)
- fix "UTF8::to_ascii()"
- update encoding list for "UTF8::str_detect_encoding()"
- use root namespaces for php functions


### 5.0.5 (2018-02-14)
- update -> "require-dev" -> "phpunit"


### 5.0.4 (2018-01-07)
- performance optimizing
  -> use "UTF8::normalize_encoding()" if needed
  -> use "CP850" encoding only if needed
  -> don't use "UTF8::html_encode()" in a foreach-loop


### 5.0.3 (2018-01-02)
- fix tests without "finfo" (e.g. appveyor - windows)
- optimize "UTF8::str_detect_encoding()"
  -> return "false" if we detect binary data, but not for UTF-16 / UTF-32


### 5.0.2 (2018-01-02)
- optimize "UTF8::is_binary()" v2
- edit "UTF8::clean()" -> do not remote diamond question mark by default
  -> fix for e.g. UTF8::file_get_contents() + auto encoding detection


### 5.0.1 (2018-01-01)
- optimize "UTF8::is_binary()" + new tests


### 5.0.0 (2017-12-10)
- "Fixed symfony/polyfill dependencies"

-> this is a breaking change, because "symfony/polyfill" contains more dependencies as we use now

before:
    "symfony/polyfill-apcu": "~1.0",
    "symfony/polyfill-php54": "~1.0",
    "symfony/polyfill-php55": "~1.0",
    "symfony/polyfill-php56": "~1.0",
    "symfony/polyfill-php70": "~1.0",
    "symfony/polyfill-php71": "~1.0",
    "symfony/polyfill-php72": "~1.0",
    "symfony/polyfill-iconv": "~1.0",
    "symfony/polyfill-intl-grapheme": "~1.0",
    "symfony/polyfill-intl-icu": "~1.0",
    "symfony/polyfill-intl-normalizer": "~1.0",
    "symfony/polyfill-mbstring": "~1.0",
    "symfony/polyfill-util": "~1.0",
    "symfony/polyfill-xml": "~1.0"
        
after:
    "symfony/polyfill-php72": "~1.0",
    "symfony/polyfill-iconv": "~1.0",
    "symfony/polyfill-intl-grapheme": "~1.0",
    "symfony/polyfill-intl-normalizer": "~1.0",
    "symfony/polyfill-mbstring": "~1.0"


### 4.0.1 (2017-11-13)
- update php-unit to 6.x


### 4.0.0 (2017-11-13)
- "php": ">=7.0"
  * drop support for PHP < 7.0
  * use "strict_types"
  * "UTF8::number_format()" -> removed deprecated method 
  * "UTF8::normalize_encoding()" -> change $fallback from bool to empty string
