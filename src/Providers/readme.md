# Development Notes

Providers consist of 3 files plus entries in Laravel's app/config/app.php file.

- Actual class file (e.g., Cnp.php) that implements the functionality being provided
- Provider class file (e.g., CnpProvider.php) that provides a register() method that can be called to instantiate the actual class
- Facade class file (e.g., CnpFacade.php) that allows functions in the actual class to be called as if static

The provider class must be registered in the 'providers' array in app.php. An alias may be registered in the aliases array in app.php. We aliased CNP to CnpFacade so that use is short and intuitive, but the actual class name says what it actually is.
