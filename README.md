# LNPAYWALL

Adds a LN Paywall for any generic webservice.

**WATCH THE DEMO VIDEO**

https://github.com/uwla/lnpaywall/assets/47862859/5e2e5b5f-9764-48a4-88cc-f5f5d553f6ea

## How it works

The user has to pay some amount of money to use the service for a chosen period of time:

1. User selects for how long he wishes to access the service
2. User pays invoice
3. Payment is confirmed
4. User can access the requested webservice
5. After time expires, user has to go back to step 1.

## Advantages

It can be integrated with **ANY** webservice **WITHOUT CHANGES** to the service.

It just requires setting up the reverse-proxy to point to the machine hosting the webservice

The server providing the webservice is not aware of the proxy.

This separation of concerns frees the developer from the burden of setting up plugins, libraries and new APIs.

The simpler approach (currently) is to change 4 lines of code in docker-compose.yml to point to an existing container running the webservice.

## tech stack

- nginx (reverse proxy)
- typescript + nodejs (REST server for lightning queries)
- laravel (backend for handling user session)
- docker (container technology)
- polar (simulate a local LN network)

## Notes

This is a proof-of-concept only coded in two days.
