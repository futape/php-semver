#SemVer

A PHP library for working with *Semantic Versioning* strings aka. [*SemVer*](http://semver.org/).



##API

The library's functions are available via the `SemVer` class. That class is defined in the `futape\semver` namespace.  
It can be included the following way.

    require_once "<path-to-src>/futape/semver/SemVer.php";
    
    use futape\semver\SemVer;

where `<path-to-src>` is the path of the directory you have placed the SemVer source in.

###Instance functions

####`__construct()`

    void __construct( string $semVer )

The `SemVer` class's constructor.  
If the passed SemVer string doesn't match the specification at <http://semver.org/>, an [`InvalidArgumentException`](http://php.net/manual/en/class.invalidargumentexception.php) is thrown.

<dl>
    <dt><code>$semVer</code></dt>
    <dd>The SemVer string represented by the created object.</dd>
</dl>

####`__toString()`

    string __toString()

A *magic* function called when casting a `SemVer` object as a string.

Returns the SemVer string represented by the object.

####`getSemVer()`

    string getSemVer()

Returns the SemVer string passed to this class's constructor.

####`getVersionStr()`

    string getVersionStr()

Returns the *version info* part of the passed SemVer string.

####`getVersion()`

    int[] getVersion()

Returns the *version info* part split at `.`s.  
The array's items are casted as integers.

####`getPreStr()`

    string getPreStr()

Returns the *pre-release info* part of the passed SemVer string.

####`getPre()`

    (string|int)[] getPre()

Returns the *pre-release info* part split at `.`s.  
Items consisting of digits only are casted as integers, while others are left as strings.

####`getBuildStr()`

    string getBuildStr()

Returns the *build metadata* part of the passed SemVer string.

####`getBuild()`

    string[] getBuild()

Returns the *build metadata* part split at `.`s.

####`isDev()`

    bool isDev()

Returns whether the release associated with the SemVer string that is represented by this object is considered to be an unstable release.  
SemVer strings having a *major version number* of 0, belonging to releases in the initial development phase,
and such containing a *pre-release info* part, marking a pre-release like an alpha or beta version or a release candidate,
are treated as unstable. This corresponds to the specification and the recommendations in the FAQ at <http://semver.org/>.

Returns whether the release is unstable.

####`cmp()`

    int cmp( SemVer $compare )

Compares the `SemVer` object with another `SemVer` object passed to this function.  
If this object is considered to precede the passed one, -1 is returned. If it follows the passed one, 1 is returned instead. Otherwise, 0, indicating that the SemVer string represented by this object is equal to the one of the passed one, is returned. Thus, the order is ascending.  
This function is perfectly suitable for using it together with PHP's [`usort()`](http://php.net/manual/en/function.usort.php) function to sort an array of `SemVer` objects.

<dl>
    <dt><code>$compare</code></dt>
    <dd>The <code>SemVer</code> object to compare this object with.</dd>
</dl>

Returns whether this object precedes the passed one (`-1`), equals it (`0`) or follows it (`1`).

####`inc()`

    SemVer inc( [ int $item = 2 [, string|false|null $build_metadata = null ]] )

Increases an item of the *version info* part of the object's SemVer string.  
If the *major version number* or the *minor version number* is increased, the *patch version number* is reset to 0. When incresing the *major version number*, also the *minor version number* is reset.  
If the SemVer string contains a *pre-release info* part, nothing is increased. Instead that part is simply removed.

<dl>
    <dt><code>$item</code></dt>
    <dd>
        A number between 1 and 3 specifying the item to be increased. <code>1</code> for the <em>patch version number</em>, <code>2</code> for the <em>minor version number</em> and <code>3</code> for the <em>major version number</em>.<br />
        If the value is outside of the specified range, an <a href="http://php.net/manual/en/class.invalidargumentexception.php"><code>InvalidArgumentException</code></a> is thrown.
    </dd>
    
    <dt><code>$build_metadata</code></dt>
    <dd>
        The <em>build metadata</em> part appended to the created SemVer string.<br />
        If <code>null</code>, the old metadata are passed through to the created <code>SemVer</code> object.<br />
        <code>false</code> instructs this function to discard the <em>build metadata</em> part entirely.
    </dd>
</dl>

Returns a new `SemVer` object representing the increased SemVer string.



##System requirements

SemVer is compatible with PHP 5.3+.

The following versions of PHP have been tested.

<tabel>
    <tbody>
        <tr>
            <td>5.3.29</td>
            <td>&#x2713;</td>
        </tr>
    </tbody>
</table>



##A word about the `master` branch

This repository has two main branches, the `develop` branch and the `master` branch.  
Branch management is done using [Vincent Driessen](http://nvie.com/posts/a-successful-git-branching-model/)'s branching model, meaning that all bleeding-edge features are available on the `develop` branch, while the `master` branch contains the stable releases only. Commits on the `master` branch introducing changes to the public API are tagged with a version number.

Versioning is done using [semantic versioning](http://semver.org/). This means that a version identifier consists of three parts, the first one being the *major* version number, the second one the *minor* version number and the third one speciying the *patch* number, separated by dots. Whenever a API-incompatible change is introduced, the major version is number increased. If the change is backwards-compatible to the public API, the minor version number is increased. A hotfix to the source increases the patch number.

A list of releases can be seen [here](https://github.com/futape/php-semver/releases). Please note, that releases with a major version number of 0 belong to the initial development phase and are not considered to be absolutely stable. However, every release since version 1.0.0 is considered to be stable.



##License

The SemVer source is published under the permissive [*New* BSD License](http://opensource.org/licenses/BSD-3-Clause).  
A [copy of that license](https://github.com/futape/php-semver/blob/master/src/futape/semver/LICENSE) is located under `src/futape/semver`.

Any other content is, if not otherwise stated, published under a [Creative Commons Attribution 4.0 International License](http://creativecommons.org/licenses/by/4.0/).  
<a href="http://creativecommons.org/licenses/by/4.0/"><img alt="Creative Commons License" border="0" src="https://i.creativecommons.org/l/by/4.0/80x15.png" /></a>

The `test.php` file is released into the public domain and published under the [CC0 1.0 Universal License](http://creativecommons.org/publicdomain/zero/1.0/).  
<a href="http://creativecommons.org/publicdomain/zero/1.0/"><img src="http://i.creativecommons.org/p/zero/1.0/80x15.png" border="0" alt="CC0" /></a>



##Support

<a href="https://flattr.com/submit/auto?user_id=lucaskrause&amp;url=http%3A%2F%2Fphp-semver.futape.de" target="_blank"><img src="//button.flattr.com/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0"></a>



##Contributing

Contributing to this project is currently not available.



##Author

<table><tbody><tr><td>
    <img src="http://www.gravatar.com/avatar/118bcae2fda8b302155ad47a2bfda556.png?s=100&amp;d=monsterid" />
</td><td>
    Lucas Krause (<a href="https://twitter.com/futape">@futape</a>)
</td></tr></tbody></table>

For a full list of contributors, click [here](https://github.com/futape/php-semver/graphs/contributors).
