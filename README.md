## Example project to demonstrate unexpected behaviour in phpunit

### Context
This is a small symfony project using the security bundle to authenticate users.
Imagine that the `refreshUser` method of the `UserProvider` would do some complex logic, involving calling 
an api endpoint of another server.
We want to mock this api call in tests to be independent of that other server.
For this purpose, the api call is alienated into its own class `ExternalApiClient`.

Testing the application, there is the some unexpected behaviour, which is demonstrated in the RefreshUserWebTest.

In this minimal example project, the `ExternalApiClient` throws an exception when it is called instead of really performing an api call.
This way, we can see when mocking the class fails unexpectedly.

**Unexpected behaviour:**

1. Requests in WebTests do not refresh the user
2. Interacting with the test container's `security.token_storage` however does trigger a user refresh (and thus an exception),
   but _only_ when performed after a request
3. When mocking the `ExternalApiClient`, the container contains a mock after a first request but not after the second
   

To see this in action, please take a look at the `RefreshUserWebTest` and run 
```
php bin/phpunit
```