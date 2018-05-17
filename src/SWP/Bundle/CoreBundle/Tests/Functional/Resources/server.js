const jsonServer = require('json-server');
const server = jsonServer.create();
const router = jsonServer.router('db.json');
const middlewares = jsonServer.defaults();

server.use(middlewares);
server.use(jsonServer.bodyParser);

let postsNumber = 1;
let postVisible = false;
// Add custom routes before JSON Server router
server.post('/wp-json/wp/v2/posts', (req, res) => {
  res.status(201).json({
    'id': postsNumber,
    'link': 'localhost:3000/wordpress/test_post',
    'featured_media': postsNumber
  });
  postsNumber++;
});

server.post('/wp-json/wp/v2/posts/:id', (req, res) => {
  if (req.body.status === 'publish') {
    postVisible = true;
  } else if (req.body.status === 'draft') {
    postVisible = false;
  }

  res.status(200).json({
    'id': req.param('id'),
    'link': 'localhost:3000/wordpress/test_post',
    'featured_media': req.param('id'),
    'status': req.body.status
  });
});

server.get('/wordpress/test_post', (req, res) => {
  if (postVisible) {
    res.sendStatus(200);
  } else {
    res.sendStatus(404);
  }
});

server.use('/api', router);
server.listen(3000, () => {
  console.log('JSON Server is running');
});