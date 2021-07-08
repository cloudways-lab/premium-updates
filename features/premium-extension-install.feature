Feature: List premium Wordpress plugins

  @require-wp-5.1.1
  Scenario: Installing Extensions theme or plugin
    Given a WP install
    And I run `wp package install "{PROJECT_DIR}:main"`

    When I try `wp theme install test-ext --activate`
    Then STDERR should be:
			"""
			Warning: test-ext: Theme not found
			Warning: The 'test-ext' theme could not be found.
			Error: No themes installed.
			"""

    When I try `wp plugin install test-ext --activate`
    Then STDERR should be:
			"""
			Warning: test-ext: Plugin not found.
			Warning: The 'test-ext' plugin could not be found.
			Error: No plugins installed.
			"""
