<?php

namespace Drupal\custom_gen\TwigExtension;

use Drupal\Core\Render\Renderer;

/**
 * Class DefaultTwigExtension.
 */
class DefaultTwigExtension extends \Twig_Extension {

        
   /**
    * {@inheritdoc}
    */
    public function getTokenParsers() {
      return [];
    }

   /**
    * {@inheritdoc}
    */
    public function getNodeVisitors() {
      return [];
    }

   /**
    * {@inheritdoc}
    */
    public function getFilters() {
      return [];
    }

   /**
    * {@inheritdoc}
    */
    public function getTests() {
      return [];
    }

   /**
    * {@inheritdoc}
    */
    public function getFunctions() {
      return [
        new \Twig_SimpleFunction('custom_gen_logo', [$this, 'custom_gen_logo']),
        new \Twig_SimpleFunction('custom_gen_menu', [$this, 'custom_gen_menu']),
        new \Twig_SimpleFunction('custom_gen_username', [$this, 'custom_gen_username']),
        new \Twig_SimpleFunction('custom_gen_uid', [$this, 'custom_gen_uid']),
      ];
    }

   /**
    * {@inheritdoc}
    */
    public function getOperators() {
      return [];
    }

   /**
    * {@inheritdoc}
    */
    public function getName() {
      return 'custom_gen.twig.extension';
    }

    public function custom_gen_logo() {
      return file_url_transform_relative(file_create_url(theme_get_setting('logo.url')));
    }

    public function custom_gen_menu($menu_name) {
    $uid = \Drupal::currentUser()->id();

    $roles = \Drupal::currentUser()->getRoles();

    if (in_array('loan', $roles) && !in_array('administrator', $roles)) {
      $menu_name = 'loan-system';
    }

    $menu_tree = \Drupal::menuTree();

    // Build the typical default set of menu tree parameters.
    $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);

    // Load the tree based on this set of parameters.
    $tree = $menu_tree->load($menu_name, $parameters);
    
    // Transform the tree using the manipulators you want.
    $manipulators = array(
      // Only show links that are accessible for the current user.
      array('callable' => 'menu.default_tree_manipulators:checkAccess'),
      // Use the default sorting of menu links.
      array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
    );
    $tree = $menu_tree->transform($tree, $manipulators);

    // Finally, build a renderable array from the transformed tree.
    $menu = $menu_tree->build($tree);

    $menu['#attributes']['class'] = 'menu navbar-nav ' . $menu_name;

    return array('#markup' => drupal_render($menu));
  }

  public function custom_gen_username() {
    $uid = \Drupal::currentUser()->id();

    $username = \Drupal::database()->query("SELECT name FROM users_field_data WHERE uid = " . $uid)->fetchField();

    return $username;
  }

  public function custom_gen_uid() {
    return \Drupal::currentUser()->id();
  }
}
