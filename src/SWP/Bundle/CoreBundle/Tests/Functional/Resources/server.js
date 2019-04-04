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

// article preview webhook url
server.post('/return-preview-url', (req, res) => {
    res.status(200).json({
        'url': 'http://localhost:3000/api/my-preview-url',
    });
});

let webhookResult = {};

// article update webhook
server.post('/article-update', (req, res) => {
    webhookResult = req.body;

    res.status(200).json({});
});

server.get('/article-update-check', (req, res) => {
    res.status(200).json(webhookResult);
});

server.get('/my-preview-url', (req, res) => {
    res.status(200).json({});
});

// Payments Hub mock
server.post('/api/v1/login_check', (req, res) => {
    res.status(200).json({
        token: '12345678',
    });
});

server.get('/public-api/v1/subscriptions/', (req, res) => {
    let id = 79;
    let articleId = 20;
    let routeId = 10;
    let name = 'secured';

    if (req.query.criteria && req.query.criteria['metadata.articleId']) {
        id = 12;
        name = 'premium_content';
        articleId = req.query.criteria['metadata.articleId'];
    }

    if (req.query.criteria && req.query.criteria['metadata.routeId']) {
        id = 14;
        routeId = req.query.criteria['metadata.routeId'];
    }

    res.status(200).json({
        _embedded: {
            items: [
                {
                    id: id,
                    type: "recurring",
                    metadata: {
                        intention: "bottom_box",
                        source: "web_version",
                        articleId: articleId,
                        routeId: routeId,
                        name: name,
                        email: 'test.user@sourcefabric.org'
                    },
                    state: 'fulfilled'
                }
            ]
        },
    });
});

server.get('/api/upload/:fileName/raw', (req, res) => {
  res.sendfile('test_file.png', {root: './'});
});

server.get('/api/upload/:fileName/audio/raw', (req, res) => {
  res.sendfile('test_audio.mp3', {root: './'});
});

server.use('/api', router);
server.listen(3000, () => {
  console.log('JSON Server is running');
});
