Assuming that in your database you have Application with id :code:`123456789` and Page with id :code:`987654321`
(and both it exists on Facebook platform), You need to call this url :code:`(route: swp_fbia_authorize)`:
:code:`/facebook/instantarticles/authorize/123456789/987654321`

In response You will be redirected to Facebook where You will need allow for all required permissions.

After that Facebook will redirect You again to application where (in background - provided by Facebook :code:`code` will
be exchanged for access token and that access) you will get JSON response with :code:`pageId` and :code:`accessToken`
(never expiring access token).