# Extension README

## Extension Management READMEs

An extension may provide per action READMEs to display in place of their primary `README.md` by including a `docs/extlc` directory in the extension and then naming the file appropriately. Options are...

* `install.md`
* `uninstall.md`
* `enable.md`
* `disable.md`
* `update.md`

These allow the extension to provide crucial information when it's needed most. For example, perhaps an extension saves some data that would be lost if it were uninstalled, so it could display a warning during uninstall to save that data first if it's needed. Or maybe there are special installation procedures that need to be followed before clicking the `Install` button.

---

[README](../README.md)