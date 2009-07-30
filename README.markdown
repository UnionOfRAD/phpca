phpca
=====

When running from the source tree, use:

`php src/phpca.php -p &lt;path&gt; &lt;file or directory&gt;`

where &lt;path&gt; is the bath to the PHP binary (required for the lint check
that is done before tokenizing the file) and &lt;file or directory&gt; is either
a single PHP file or a directory. If a directory is given, all *.php files
in that directory and its subdirectories will be analyzed.

To run from Phar archive (to create a Phar archive, run "phing phar"), use:

`php phpca.phar -p &lt;path&gt; &lt;file or directory&gt;`

The other command line switches phpca currently supports are:

  -p &lt;file&gt;
  --php &lt;file&gt;      Specify path to PHP executable (required).

  -l
  --list            List all built-in rules.

  -h
  --help            Prints this usage information.

  -v
  --version         Prints the version number.


See TODO file for development roadmap.
