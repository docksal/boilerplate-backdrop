# Docksal powered Backdrop Installation

This is a sample Backdrop CMS installation pre-configured for use with Docksal.  

Features:

- Backdrop CMS
- `fin init` example

## Setup instructions

### Step 1: Docksal environment setup

**This is a one time setup - skip this if you already have a working Docksal environment.**  

Follow [Docksal environment setup instructions](http://docksal.readthedocs.io/en/master/getting-started/env-setup)

### Step 2: Project setup

1. Clone this repo into your Projects directory

    ```
    git clone https://github.com/docksal/example-backdrop.git
    cd backdrop
    ```

2. Initialize the site

    This will initialize local settings and install the site via drush

    ```
    fin init
    ```

3. **On Windows** add `192.168.64.100  backdrop.docksal` to your hosts file

4. Point your browser to

    ```
    http://backdrop.docksal
    ```


## More automation with 'fin init'

Site provisioning can be automated using `fin init`, which calls the shell script in [.docksal/commands/init](.docksal/commands/init).  
This script is meant to be modified per project.

Some common tasks that can be handled in the init script:

- initialize local settings files for Docker Compose, Backdrop, Behat, etc.
- import DB or perform a site install
- compile Sass
- run DB updates, revert features, clear caches, etc.
- enable/disable modules, update variables values
- run Behat tests

## Updating Backdrop

My Docksal installed version of *Backdrop* is (was) a couple of versions behind on updates so I used the following process to remedy that...

I visited http:/igo-bd.docksal and login as `admin`.  The admin menu bar shows 2 problems, so I visited the link provided by the indicator, http:/igo-bd.docksal/admin/reports/status.  

A link was provided on that page to http:/igo-bd.docksal/admin/modules/update, and on that page was a link to download the necessary update.  I did so, saving the update to `~/Downloads/backdrop.zip`.  I then used *Finder* to unzip the contents creating a `~/Downloads/backdrop` folder.  

I then forked `https://github.com/docksal/example-backdrop/` to `https://github.com/SummittDweller/example-backdrop`

I subsequently performed...

```
cd ~/Stacks
rm -fr igo-bd     # remove old contents
git clone https://github.com/SummittDweller/example-backdrop igo-bd
rm -fr igo-bd/docroot/.
rsync -aruvi ~/Downloads/backdrop/. docroot/
fin init
```

It works!

The update to *Backdrop 1.11.2* has been pushed back to https://github.com/SummittDweller/example-backdrop so there should be no need to perform that update again.  Also, a PR to https://github.com/docksal/example-backdrop/ has been submitted to update the `master` branch of the upstream repo.

## Taking Steps to Update to Latest Backdrop

I opened a terminal/shell at `Mark-iMac:/Users/markmcfate/Documents/Migrations` and did...

```
git clone https://github.com/SummittDweller/example-backdrop.git backdrop
cd backdrop
fin init
```

Visit http://backdrop.docksal and it works.  Login as `admin / admin` and visit http://backdrop.docksal/admin/reports/status only to see... `Out of date (version 1.11.3 available)`.  So I clicked the link, downloaded v1.11.3 as a .zip, unpacked it, and copied the contents into my `Users/markmcfate/Documents/Migrations/backdrop/docroot` folder like so:

```
Mark-iMac:docroot markmcfate$ cp -fr ~/Downloads/backdrop/core. ./core/
```
