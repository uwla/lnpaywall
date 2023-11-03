# LNPAYWALL

Adds a LN Paywall for any generic webservice.


The user has to pay some amount of time to use service for a chosen time:

1. User selects how much time he wishes to use.
2. User pays invoice
3. Payment is confirmed
4. User can access the webservice
5. After time expires, user has to go back to step 1.

It works for any webservice because we put a Nginx reverse-proxy before the requests hits the service.

Thus, there is no need to change or alter anything in the webservice which shall be served.

This is a proof-of-concept only.

**tech stack**:

- nginx (reverse proxy)
- typescript + nodejs (REST server for lightning queries)
- laravel (backend for handling user session)
- docker (container technology)
- polar (simulate a local LN network)
