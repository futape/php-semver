<?php
/*! php-semver 1.0.0 | Copyright (c) 2015 Lucas Krause | New BSD License | http://php-semver.futape.de */

namespace futape\semver;

use Exception, InvalidArgumentException, BadMethodCallException;

class SemVer {
    
    
    
    #unrelated-functions#
    
    #SemVer::escRegex()#
    /**
     * Escapes special RegEx (PCRE) characters, as well as `/`.
     *
     * @param string $string The string to be escaped.
     *
     * @return string The escaped string.
     */
    private static function escRegex($str_a){
        return preg_quote($str_a, "/");
    }
    
    #SemVer::lstrip()#
    /**
     * Strips away a string from the beginning of another string.
     *
     * @param string $string The string to remove the other string from.
     * @param string $remove The string to remove from `$string`'s beginning.
     *
     * @return Returns the string with the other one stripped ways from its beginning.
     */
    private static function lstrip($str_a, $str_b){
        return preg_replace('/^'.self::escRegex($str_b).'/', "", $str_a);
    }
    
    #SemVer::reRepeat()#
    /**
     * Returns a RegEx pattern matching a sequence of multiple instances of the passed pattern.
     * If `$number` equals 0, the returned pattern matches a sequence of zero or more instances of the passed pattern,
     * separated by the passed separator.
     * Othwerwise, the sequence must consist of exactly as much instances of the passed pattern as the absolute value of `$number`.
     * If `$number` is lower than 0, that value defines the minimum number of instances in the sequence, more are allowed.
     *
     * @param string $pattern   The pattern upon which to build the returned pattern.
     * @param int    $number    Specifies how many instances of the passed pattern are allowed in a sequence of that pattern.
     * @param string $separator `= ""`
     *                          A string separating the instances of the passed pattern in the returned one.
     *
     * @return string The pattern matching a sequence of instances of the passed pattern.
     */
    private static function reRepeat($re_pattern, $int_num, $str_sep=""){
        $re_a=$re_pattern;
        
        if($int_num!=1){
            $int_a=abs($int_num)-1;
            
            $re_a.='(?:'.self::escRegex($str_sep).$re_pattern.')';
            $re_a.=/**/$int_a<1/*/($int_num==0 || $int_num==-1)/**/ ? '*' : '{'.$int_a.($int_num<0 ? ',' : "").'}';
            
            if($int_num==0){
                $re_a='(?:'.$re_a.')?';
            }
        }
        
        return $re_a;
    }
    
    
    
    #semver-properties#
    
    #SemVer::$semVerVersion#
    /**
     * Information about the *version info* part of a SemVer string matching the secification at <http://semver.org/>.
     * Used to build the RegEx to match a SemVer string against.
     * Must contain the following items.
     * 
     * +   `string pattern`: A RegEx pattern matching a single item of the version info part. Should never contain any captured subpatterns.
     * +   `int parts`: The number of single items. Is passed to `SemVer::reRepeat()`'s second parameter.
     * +   `string sep`: The string used to separate the single items. Is passed to `SemVer::reRepeat()`'s third parameter.
     * +   `string|null prefix`: A single character introducing the version info part. A value of `null` means that no prefix is used.
     * +   `bool optional`: Whether the whole part is optional and may be skipped.
     *
     * @type array
     */
    private static $semVerVersion=array( //version info
        "pattern"=>'(?:0|[1-9]\d*)',
        "parts"=>3,
        "sep"=>".",
        "prefix"=>null,
        "optional"=>false
    );
    
    #SemVer::$semVerPre#
    /**
     * Information about the *pre-release info* part of a SemVer string matching the secification at <http://semver.org/>.
     * Used to build the RegEx to match a SemVer string against.
     * Must contain the following items.
     * 
     * +   `string pattern`: A RegEx pattern matching a single item of the pre-release info part. Should never contain any captured subpatterns.
     * +   `int parts`: The number of single items. Is passed to `SemVer::reRepeat()`'s second parameter.
     * +   `string sep`: The string used to separate the single items. Is passed to `SemVer::reRepeat()`'s third parameter.
     * +   `string|null prefix`: A single character introducing the pre-release info part. A value of `null` means that no prefix is used.
     * +   `bool optional`: Whether the whole part is optional and may be skipped.
     *
     * @type array
     */
    private static $semVerPre=array( //pre-release info
        "pattern"=>'(?:0|[1-9]\d*|[a-zA-Z\d-]+)',
        "parts"=>-1,
        "sep"=>".",
        "prefix"=>"-",
        "optional"=>true
    );
    
    #SemVer::$semVerBuild#
    /**
     * Information about the *build metadata* part of a SemVer string matching the secification at <http://semver.org/>.
     * Used to build the RegEx to match a SemVer string against.
     * Must contain the following items.
     * 
     * +   `string pattern`: A RegEx pattern matching a single item of the build metadata part. Should never contain any captured subpatterns.
     * +   `int parts`: The number of single items. Is passed to `SemVer::reRepeat()`'s second parameter.
     * +   `string sep`: The string used to separate the single items. Is passed to `SemVer::reRepeat()`'s third parameter.
     * +   `string|null prefix`: A single character introducing the build metadata part. A value of `null` means that no prefix is used.
     * +   `bool optional`: Whether the whole part is optional and may be skipped.
     *
     * @type array
     */
    private static $semVerBuild=array( //build metadata
        "pattern"=>'(?:0|[1-9]\d*|[a-zA-Z\d-]+)',
        "parts"=>-1,
        "sep"=>".",
        "prefix"=>"+",
        "optional"=>true
    );
    
    #SemVer:$reSemVer#
    /**
     * The RegEx to match a SemVer string against.
     * A value of `null` means that this property hasn't been initialized yet.
     *
     * @type string|null
     */
    private static $reSemVer=null;
    
    
    
    #properties#
    
    #SemVer::$semVer#
    /**
     * The SemVer string passed to this class's constructor.
     *
     * @type string
     */
    private $semVer; //SemVer string
    
    #SemVer::$versionStr#
    /**
     * The *version info* part of the passed SemVer string.
     *
     * @type string
     */
    private $versionStr; //version info
    
    #SemVer::$version#
    /**
     * The *version info* part split at `.`s.
     * The array's items are casted as integers.
     *
     * @type int[]
     */
    private $version;
    
    #SemVer::$preStr#
    /**
     * The *pre-release info* part of the passed SemVer string.
     *
     * @type string
     */
    private $preStr; //pre-release info
    
    #SemVer::$pre#
    /**
     * The *pre-release info* part split at `.`s.
     * Items consisting of digits only are casted as integers,
     * while others are left as strings.
     *
     * @type (string|int)[]
     */
    private $pre;
    
    #SemVer::$buildStr#
    /**
     * The *build metadata* part of the passed SemVer string.
     *
     * @type string
     */
    private $buildStr; //build metadata
    
    #SemVer::$build#
    /**
     * The *build metadata* part split at `.`s.
     *
     * @type string[]
     */
    private $build;
    
    
    
    #init-functions#
    
    #SemVer::__construct()#
    /**
     * The `SemVer` class's constructor.
     * If the passed SemVer string doesn't match the specification at <http://semver.org/>,
     * an `InvalidArgumentException` is thrown.
     *
     * @param string $semVer The SemVer string represented by the created object.
     *
     * @return void
     */
    public function __construct($str_semVer){
        $this->semVer=$str_semVer;
        
        try{
            $this->init(); //$versionStr, $version, $preStr, $pre, $buildStr, $build, $reSemVer; relies on $semVer
        } catch(Exception $e){
            throw new InvalidArgumentException($e->getMessage(), 0, $e);
        }
    }
    
    #SemVer::init()#
    /**
     * Initializes the `$reSemVer` property if not already done,
     * as well as the `$versionStr`, `$version`, `$preStr`, `$pre`,
     * `$buildStr`, `$build` and `$reSemVer` properties.
     * Relies on the `$semVer` property. If that property's value isn't a valid SemVer string,
     * an `Exception` is thrown.
     *
     * @return void
     */
    private function init(){
        self::initSemVerRegex();
        
        if(preg_match(self::$reSemVer, $this->semVer, $arr_semVer)!=1){ //!
            throw new Exception('Invalid SemVer string "'.$this->semVer.'"');
        }
        
        $this->versionStr=$arr_semVer[1];
        $this->version=array_map("intval", explode(self::$semVerVersion["sep"], $this->versionStr));
        
        $this->preStr=$arr_semVer[2]=="" ? null : $arr_semVer[2];
        $this->pre=is_null($this->preStr) ? null : array_map(function($val){
            return preg_match('/^\d+$/', $val)==1 ? (int)$val : $val;
        }, explode(self::$semVerPre["sep"], $this->preStr));
        
        $this->buildStr=$arr_semVer[3]=="" ? null : $arr_semVer[3];
        $this->build=is_null($this->buildStr) ? null : explode(self::$semVerBuild["sep"], $this->buildStr);
    }
    
    #SemVer::initSemVerRegex()#
    /**
     * Initializes the `$reSemVer` property.
     * The pattern is build upon the values of `$semVerVersion`, `$semVerPre` and `$semVerPre`.
     *
     * @return void
     */
    private static function initSemVerRegex(){
        if(!is_null(self::$reSemVer)){
            return;
        }
        
        $re_semVer='/^';
        
        foreach(array(
            self::$semVerVersion,
            self::$semVerPre,
            self::$semVerBuild
        ) as $val){
            if(!is_null($val["prefix"])){
                $re_semVer.=self::escRegex($val["prefix"]).'?'/** /.'+'/**/;
            }
            
            $re_semVer.='((?:';
            
            if(!is_null($val["prefix"])){
                /** /
                if($val["optional"]){
                    $re_semVer.='(?<!'.self::escRegex($val["prefix"]).')|';
                }else{
                    $re_semVer.='(?<='.self::escRegex($val["prefix"]).')';
                }
                /*/
                if($val["optional"]){
                    $re_semVer.='(?<!'.self::escRegex($val["prefix"]).')|';
                }
                $re_semVer.='(?<='.self::escRegex($val["prefix"]).')';
                /**/
            }
            
            $re_semVer.=self::reRepeat($val["pattern"], $val["parts"], $val["sep"]);
            $re_semVer.=')';
            
            if(is_null($val["prefix"])){
                if($val["optional"]){
                    $re_semVer.='?';
                }
            }
            
            $re_semVer.=')';
        };
        
        $re_semVer.='$/';
        
        self::$reSemVer=$re_semVer;
    }
    
    
    
    #getter-functions#
    
    #SemVer::__call()#
    /**
     * A *magic* overloading function that gets called when an object's non-reachable function is called.
     * This function is used to emulate global getter functions for some of the object's properties.
     * The following getters are available:
     *
     * +   `string getSemVer()`: The SemVer string passed to this class's constructor (`$semVer` property).
     * +   `string getVersionStr()`: The *version info* part of the passed SemVer string (`$versionStr` property).
     * +   `int[] getVersion()`: The *version info* part split at `.`s (`$version` property).
     * +   `string getPreStr()`: The *pre-release info* part of the passed SemVer string (`$preStr` property).
     * +   `(string|int)[] getPre()`: The *pre-release info* part split at `.`s (`$pre` property).
     * +   `string getBuildStr()`: The *build metadata* part of the passed SemVer string (`$buildStr` property).
     * +   `string[] getBuild()`: The *build metadata* part split at `.`s (`$build` property).
     *
     * If any other non-reachable function is called, a `BadMethodCallException` exception is thrown.
     *
     * @param string $function_name The name of the called function.
     * @param array  $arguments     The arguments passed to the called function.
     *
     * @return mixed
     */
    public function __call($str_fn, $arr_args){
        $str_getterPrefix="get";
        $arr_getters=array_flip(array_map(function($val) use ($str_getterPrefix){
            return $str_getterPrefix.ucfirst($val);
        }, array(
            "semVer",
            
            "versionStr",
            "version",
            
            "preStr",
            "pre",
            
            "buildStr",
            "build"
        )));
        
        if(array_key_exists($str_fn, $arr_getters)){
            return $this->{lcfirst(self::lstrip($str_fn, $str_getterPrefix))};
        }
        
        throw new BadMethodCallException('Method "'.@array_pop(explode("\\", __CLASS__))."::".$str_fn.'()" doesn\'t exist.');
    }
    
    #SemVer::__toString()#
    /**
     * A *magic* function called when casting a `SemVer` object as a string.
     *
     * @return string Returns the SemVer string represented by the object.
     */
    public function __toString(){
        return $this->semVer;
    }
    
    #SemVer::isDev()#
    /**
     * Returns whether the release associated with the SemVer string that is represented by this object
     * is considered to be an unstable release.
     * SemVer strings having a *major version number* of 0, belonging to releases in the initial development phase,
     * and such containing a *pre-release info* part, marking a pre-release like an alpha or beta version or a release candidate,
     * are treated as unstable. This corresponds to the specification and the recommendations in the FAQ at <http://semver.org/>.
     *
     * @return bool Whether the release is unstable.
     */
    public function isDev(){
        return (!is_null($this->pre) || $this->version[0]==0); //pre-release or initial development phase (major version 0)
    }
    
    
    
    #semver-functions#
    
    #SemVer::cmp()#
    /**
     * Compares the `SemVer` object with another `SemVer` object passed to this function.
     * If this object is considered to precede the passed one, -1 is returned.
     * If it follows the passed one, 1 is returned instead.
     * Otherwise, 0, indicating that the SemVer string represented by this object
     * is equal to the one of the passed one, is returned.
     * Thus, the order is ascending.
     * This function is perfectly suitable for using it together with PHP's `usort()` function
     * to sort an array of `SemVer` objects.
     *
     * @param SemVer $compare The `SemVer` object to compare this object with.
     *
     * @return int Returns whether this object precedes the passed one (`-1`), equals it (`0`)
     *             or follows it (`1`).
     */
    public function cmp($semVer_a){ //asc
        $arr_version=$this->version;
        $arr_pre=$this->pre;
        
        $arr_version_b=$semVer_a->getVersion();
        $arr_pre_b=$semVer_a->getPre();
        
        foreach($arr_version as $i=>$val){
            $val_b=$arr_version_b[$i];
            
            if($val!=$val_b){
                return $val>$val_b ? 1 : -1;
            }
        }
        
        if(is_null($arr_pre)!=is_null($arr_pre_b)){
            return !is_null($arr_pre) ? -1 : 1;
        }else if(is_null($arr_pre) /** /&& is_null($arr_pre_b)/**/){
            return 0;
        }
        
        for($i=0; $i<min(count($arr_pre), count($arr_pre_b)); $i++){
            $val=$arr_pre[$i];
            $val_b=$arr_pre_b[$i];
            
            if(is_int($val)!=is_int($val_b)){
                return is_int($val) ? -1 : 1;
            }else if($val!=$val_b){
                if(is_int($val) /** /&& is_int($val_b)/**/){
                    return $val>$val_b ? 1 : -1;
                }else /** /if(is_string($val) && is_string($val_b))/**/{
                    return max(min(strcmp($val, $val_b), 1), -1);
                }
            }
        }
        
        if(count($arr_pre)!=count($arr_pre_b)){
            return count($arr_pre)>count($arr_pre_b) ? 1 : -1;
        }
        
        return 0;
    }
    
    #SemVer::inc()#
    /**
     * Increases an item of the *version info* part of the object's SemVer string.
     * If the *major version number* or the *minor version number* is increased, the *patch version number* is reset to 0.
     * When incresing the *major version number*, also the *minor version number* is reset.
     * If the SemVer string contains a *pre-release info* part, nothing is increased. Instead that part is simply removed.
     *
     * @param int $item                         `= 2`
     *                                          A number between 1 and 3 specifying the item to be increased.
     *                                          `1` for the *patch version number*, `2` for the *minor version number* and `3` for the *major version number*.
     *                                          If the value is outside of the specified range, an `InvalidArgumentException` is thrown.
     * @param string|false|null $build_metadata `= null`
     *                                          The *build metadata* part appended to the created SemVer string.
     *                                          If `null`, the old metadata are passed through to the created `SemVer` object.
     *                                          `false` instructs this function to discard the *build metadata* part entirely.
     *
     * @return SemVer A new `SemVer` object representing the increased SemVer string.
     */
    public function inc($int_item=2, $str_build=null){ //increase
        if($int_item<1 || $int_item>3){
            throw new InvalidArgumentException('"'.@array_pop(explode("\\", __METHOD__)).'()" expects first argument to be a number between 1 and 3.');
        }
        
        $str_version=$this->versionStr;
        $str_pre=$this->preStr;
        $str_build=is_null($str_build) ? $this->buildStr : $str_build;
        $str_build=$str_build===false ? null : $str_build;
        
        if(!is_null($str_pre)){
            $str_pre=null;
        }else{
            $arr_version=$this->version;
            
            $arr_version[count($arr_version)-$int_item]++;
            
            $int_a=$int_item-1;
            
            if($int_a>0){
                array_splice($arr_version, -$int_a, $int_a, array_fill(0, $int_a, 0));
            }
            
            $str_version=implode(self::$semVerVersion["sep"], $arr_version);
        }
        
        return new self(
            $str_version.
            (!is_null($str_pre) ? self::$semVerPre["prefix"].$str_pre : "").
            (!is_null($str_build) ? self::$semVerBuild["prefix"].$str_build : "")
        );
    }
    
    
    
}
