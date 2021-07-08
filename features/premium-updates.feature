Feature: Premium Updates integration is present and active

  Scenario: Running --debug on command to be intercepted
    Given a WP install

    When I try `wp plugin list --debug`
    Then STDERR should contain:
    """
    Registering Cloudways premium updates package
    """
    And STDERR should contain:
    """
    Detected a command to be intercepted: plugin list
    """
    And STDERR should not contain:
    """
    Skipping registration of Cloudways premium updates package for this command
    """
    And STDERR should contain:
    """
    Faking an admin request
    """
    And STDERR should contain:
    """
    About to set site transient "update_plugins" - last_checked:
    """

  Scenario: Running --debug on command not to be intercepted
    Given a WP install

    When I try `wp post list --debug`
    Then STDERR should contain:
    """
    Registering Cloudways premium updates package
    """
    And STDERR should not contain:
    """
    Detected a command to be intercepted: plugin list
    """
    And STDERR should contain:
    """
    Skipping registration of Cloudways premium updates package for this command
    """
    And STDERR should not contain:
    """
    Faking an admin request
    """
    And STDERR should not contain:
    """
    About to set site transient "update_plugins" - last_checked:
    """