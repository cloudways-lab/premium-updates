Cloudways WP-CLI Premium Updates
================================

Makes premium plugin & theme updates work transparently via WP-CLI. 

Quick links: [Using](#using) | [Installing](#installing) | [Contributing](#contributing)

## Using

No specific action is required to make use of this package. As soon as the package is installed and active, it does its work in the background and adapts calls to the following commands so that they work as expected with premium plugins & themes:

* **`plugin list`**
* **`plugin update`**
* _...more to follow_

To verify that the package is active, you can append the `--debug` flag to the above commands and you should then see debug messages grouped under `premium-updates`. You can also filter the debug output to this package only with the `--debug=premium-updates` flag.

## Installing

To install the latest stable version of this package, run:

```bash
wp package install cloudways-lab/premium-updates@stable
```

To install the latest development version of this package, run:

```bash
wp package install cloudways-lab/premium-updates
```

## Contributing

We appreciate you taking the initiative to contribute to this project.

Contributing isn’t limited to just code. We encourage you to contribute in the way that best fits your abilities, by writing tutorials, giving a demo at your local meetup, helping other users with their support questions, or revising our documentation.

### Reporting a bug

Think you’ve found a bug? We’d love for you to help us get it fixed.

Before you create a new issue, you should [search existing issues](https://github.com/cloudways-lab/premium-updates/issues?q=label%3Abug%20) to see if there’s an existing resolution to it, or if it’s already been fixed in a newer version.

Once you’ve done a bit of searching and discovered there isn’t an open or fixed issue for your bug, please [create a new issue](https://github.com/cloudways-lab/premium-updates/issues/new). Include as much detail as you can, and clear steps to reproduce if possible.

### Creating a pull request

Want to contribute a new feature? Please first [open a new issue](https://github.com/cloudways-lab/premium-updates/issues/new) to discuss whether the feature is a good fit for the project.
