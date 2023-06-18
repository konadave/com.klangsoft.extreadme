# Extension README

This extension provides a simple mechanism for other extensions to include functional Markdown documents in their user interface. As a demonstration, while also providing a useful feature, this extension will embed the `README.md` of an extension on management pages; i.e. install, enable, disable, uninstall, update. This extension also demonstrates [how to provide an alternative README for each action](docs/altlc.md).

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.4+
* CiviCRM 5.? (Pretty early probably)

## Installation (Web UI)

Learn more about installing CiviCRM extensions in the [CiviCRM Sysadmin Guide](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/).

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl com.klangsoft.extreadme@https://github.com/konadave/com.klangsoft.extreadme/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/konadave/com.klangsoft.extreadme.git
cv en extreadme
```

## Getting Started

As a consumer of the extension, you're done! Extension READMEs will show up where they're intended with no additional work on your part.

Are you an extension developer? Check out the [simple guide to adding Markdown](docs/simple.md) to your extension.

## Support

Want to provide some feedback? Did you find an issue that needs addressed? Do you have a feature request or suggestion for the documentation? Please check out the <a href="https://github.com/konadave/com.klangsoft.extreadme/issues" target="_blank">issue queue</a> to create a new issue or chime in on an existing one.

Are you a developer looking to help out with some solid code? Please check out the <a href="https://github.com/konadave/com.klangsoft.extreadme/issues" target="_blank">issue queue</a>, work up a merge request, and submit for consideration. All help is appreciated.

Are you an organization with some funds to spare and would like to aid the continued development of this and other quality CiviCRM extensions? Consider contributing to the "tip jar" by scanning the following PayPal QR code. Please don't feel obligated to contribute one of the suggested amounts; give what you can, any amount is both helpful and appreciated. Thanks!

![Tips are appeciated!](images/qrcode.png)