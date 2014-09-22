# Инсталлер Opencart

Добро пожаловать в скромный репозиторий инсталлера Opencart.
Инсталлер позволяет скромно установить Opencart в произвольную директорию.

# Использование

Opencart пока еще недоступен на packagist (а еще инсталлеру необходим
специальный тип пакета, `opencart-core`), поэтому придется задать его как
репозиторий в `composer.json`:

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

После этого его необходимо объявить зависимостью вместе с
`etki/opencart-core-installer`:

```js
"require": {
    "etki/opencart-installer": "~0.1",
    "opencart/opencart": "1.5.6.3"
}
```

*(безусловно, можно использовать любую версию Opencart)*

Последнее, что необходимо сделать - указать папку установки Opencart в секции
`extra`:

```js
"extra": {
    "opencart-install-dir": "src/public"
}
```

После этого код Opencart попадет прямо в `src/public`. Гип-гип.
*Замечание: если не указывать опцию `opencart-install-dir`, то инсталлер будет
использовать папку `opencart` по умолчанию.*
