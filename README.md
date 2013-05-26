CI_Pagination
=============

Rewrite CI_Pagination class to be more compatible with different URI segments, languages and configurations.

How to use
==========

You can use a 'pagination' config file or call the method 'initialize' with an array containing parameters.
All the configurations are merged. You don't need to pass all the parameters you've set in the config file.

To print links, call <code>echo $this->pagination->links();</code>.

Twitter Bootstrap compatibility
===============================

Now you can use Twitter Bootsrap pagination (http://twitter.github.io/bootstrap/components.html#pagination).

To print links, call <code>echo $this->pagination->bootstrap_links();</code>.
