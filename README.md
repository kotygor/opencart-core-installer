# Opencart installer

Oh hi.

This is a simple composer plugin that allows to install opencart in a custom
directory.

# Usage

Opencart is not available through Composer yet (and it also requires custom
`opencart-core` package type to be installed by this installer), so you have to
specify it manually as a repository in your `composer.json`:
# Why it's bad idea use this way
See https://stackoverflow.com/a/14485706

```js
"repositories": [
    {
        "type": "package",
        "package": {
            "name": "opencart/opencart",
            "type": "opencart-core",
            "version": "1.5.6.3",
            "dist": {
                "url": "https://github.com/opencart/opencart/archive/1.5.6.3.zip",
                "type": "zip"
            },
            "source": {
                "url": "https://github.com/opencart/opencart",
                "type": "git",
                "reference": "1.5.6.3"
            }
        }
    }
],
```

After that you have to require it alongside with `etki/opencart-core-installer`:

```js
"require": {
    "etki/opencart-core-installer": "~0.1",
    "opencart/opencart": "1.5.6.3"
}
```

*(feel free to use any Opencart version you like)*

The last step is to specify install directory in `extra` section:

```js
"extra": {
    "opencart-install-dir": "src/public"
}
```

Now Opencart source code will be placed in `src/public` folder. Hooray!

*Note: if you omit `opencart-install-dir` extra option, installer will use
`opencart` folder as a default.*
