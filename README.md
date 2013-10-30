Gaufrette Browser
=================

[![Build Status](https://travis-ci.org/digitalkaoz/GaufretteBrowserBundle.png)](https://travis-ci.org/digitalkaoz/GaufretteBrowserBundle)
[![Total Downloads](https://poser.pugx.org/digitalkaoz/gaufrette-browser-bundle/downloads.png)](https://packagist.org/packages/digitalkaoz/gaufrette-browser-bundle)
[![Latest Stable Version](https://poser.pugx.org/digitalkaoz/gaufrette-browser-bundle/v/stable.png)](https://packagist.org/packages/digitalkaoz/gaufrette-browser-bundle)
[![Code Coverage](https://scrutinizer-ci.com/g/digitalkaoz/GaufretteBrowserBundle/badges/coverage.png?s=5871e797862c67c2efbeaf0c9d3d9d1115d94a1b)](https://scrutinizer-ci.com/g/digitalkaoz/GaufretteBrowserBundle/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/digitalkaoz/GaufretteBrowserBundle/badges/quality-score.png?s=c23fb208a0ee64cfdb844c8794973352c9417169)](https://scrutinizer-ci.com/g/digitalkaoz/GaufretteBrowserBundle/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/9d3914c2-0636-4d7c-a560-dfea413baa93/mini.png)](https://insight.sensiolabs.com/projects/9d3914c2-0636-4d7c-a560-dfea413baa93)

This Bundle allows you to browse a Gaufrette Filesystem like a Doctrine Connection.
It tries to wrap Doctrine ObjectRepositories and Entities around Gaufrette Files.

Installation
------------

**Install it with Composer**

``` json
{
    "require" : {
        "digitalkaoz/gaufrette-browser-bundle" : "dev-master@dev"
    }
}
```

**Active the Bundle in your Kernel**

``` php
// application/AppKernel.php
public function registerBundles()
{
  return array(
      // ...
      new rs\GaufretteBrowserBundle\rsGaufretteBrowserBundle(),
      // ...
  );
}
```

**Import the Routing**

``` yml
rs_gaufrette_browser:
    resource: "@rsGaufretteBrowserBundle/Resources/config/routing.xml"
    prefix:   /gaufrette/browser
```

Configuration
-------------

**Create a Gaufrette Filesystem (see https://github.com/KnpLabs/KnpGaufretteBundle for more Installation and Configuration)**

``` yml
knp_gaufrette:
    adapters:
        default:
            local:
                directory: /your/path/
    filesystems:
        default:
            adapter:    default
            alias:      default_filesystem

    stream_wrapper: ~
```

**Connect the Browser to the Filesystem**

``` yml
rs_gaufrette_browser:
    filesystem: default_filesystem #the gaufrette filesystem alias to use
```


**The Full Config Reference**

``` yml
rs_gaufrette_browser:
    file_pattern:    "/\.(jpg|gif|jpeg|png)$/"    # a valid regular expression to filter for file-extensions
    filesystem:      default_filesystem           # the gaufrette filesystem alias to use
    file_class:      Your\File\Class              # should extend rs\GaufretteBrowserBundle\Entity\File
    directory_class: Your\Directory\Class         # should extend rs\GaufretteBrowserBundle\Entity\Directory
```


Usage
-----

Goto http://your.domain/gaufrette/browser and browse the filesystem

**Querying for Directories or Files**

Using the Repository Implementations:

``` php
$this->get('rs_gaufrette_browser.repository.directory')->findBy(array('prefix'=>'/foo')); #only search folders that starts with /foo
$this->get('rs_gaufrette_browser.repository.file')->findBy(array('suffix'=>'/\.xls/')); #only search .xls files
$this->get('rs_gaufrette_browser.repository.file')->find('/foo/bar.png'); #find one file
$this->get('rs_gaufrette_browser.repository.file')->findOneBy(array('prefix'=>'/foo', 'suffix' => '/\.xls/')); #find one file named /foo/*.xls
```

**Using the ParamConverters**

Using ParamConverters for `Directory` and `File` is quiet simple:

``` php
/**
 * @ParamConverter("folder", class="rs\GaufretteBrowserBundle\Entity\Directory", options={"id" = "slug"})
 */
public function myCustomDirectoryAction(Request $request, Directory $folder){}

/**
 * @ParamConverter("file", class="rs\GaufretteBrowserBundle\Entity\File", options={"id" = "slug"})
 */
public function myCustomFileAction(Request $request, File $file){}
```

**Hooking into Events**

Everything inside the Controller-Actions is build up with Events.

**The following Events exists**

``` php
final class GaufretteBrowserEvents
{
    const DIRECTORY_SHOW = 'gaufrette.directory.show';
    const DIRECTORY_INDEX = 'gaufrette.directory.index';

    const FILE_SHOW = 'gaufrette.file.show';
    const FILE_INDEX = 'gaufrette.file.index';
}
```

**Hook your own Functionality into default Controllers**

implement the Subscriber

``` php
class MyEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            GaufretteBrowserEvents::DIRECTORY_INDEX => 'myCustomAction'
        );
    }

    public function myCustomAction(DirectoryControllerEvent $event)
    {
        $event->addDirectory($myCustomDirectory);
        $event->removeDirectory($unwantedDirectory);

        $event->addTemplateData('myCustomVar', 'foobar');
    }
}
```

and TAG the Service

``` xml
<service id="my.custom.event_subscriber" class="\MyEventSubscriber">
    <tag name="kernel.event_subscriber" />
</service>
```


Tests
-----

everything is well unit tested:

``` sh
phpunit
```

view builds on Travis: https://travis-ci.org/digitalkaoz/GaufretteBrowserBundle

TODO
----

* better Tree Initialization of Directories (maybe https://github.com/KnpLabs/materialized-path)
* Javascript-Tree to Browse Files more sophisticated (maybe http://jquery.bassistance.de/treeview/demo/)
* better File Detail Pages
* functional Tests
* allow more gaufrette-filesystem instances
* more powerful Repository Queries
