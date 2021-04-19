# Subscriber - Server to Servers Notification

**Built with:** PHP/Laravel 8

This is a high level summary of the Publisher and Subscribers APIs and communication between them.

- **Publisher**: https://github.com/ZestArinze/temp-publisher
- **Subscriber**: https://github.com/ZestArinze/temp-subscriber

## Testing

After the setup described below, run the projects at different ports:

**Example**

- Publisher: php artisan serve --port=9000
- Subscriber: php artisan serve --port=8000

**Automated Tests**
php artisan test

**Manual Testing**
Call the endpoints of interest. The endpoints are also described below with sample requests and responses.

## Subscriber Server

### Setup

#### Codebase

- Clone the repo: https://github.com/ZestArinze/temp-subscriber
- Connect to the database in .env file
- Run the commands:
  - composer install
  - php artisan migrate
  - php artisan key:generate

### .env Config

In addition to the connection to the database, add the following to the .env file:

- PUBLISHER_API_BASE_URL=your-correct-client_api_base_url
- - **Example**: http://127.0.0.1:9000/api
- PUBLISHER_CLIENT_ID=client_id_from_publisher
- PUBLISHER_CLIENT_SECRET=client_secret_from_publisher

### Subscribe to Notifications

Subscribe to notifications by calling the publisher endpoint thus:

**Note**: The actual request sent from the subscriber backend is different from what is sent from the subscriber client end. For example, the backend includes the callback URL and the HMAC and client ID in the request header.

**_Subscriber Backend_**

The actual endpoint called behind the scene at the subscriber’s backend:

**Endpoint**:
POST your\*correct_publisher_subscription_api_endpoint

**Example**:
POST http://127.0.0.1:9000/api/subscribe

**Subscriber Client**

**Endpoint**:
GET /api/subscribe/:topicUniqueIdentifier

**Response**:
{
"status": true,
"message": "Subscription successful.",
"data": {
"topic": "Body Wash",
"url": "http://127.0.0.1:8000/api/webhooks/posts",
"identifier": "body-wash"
},
"error": null
}

### Get the posts (sent by the Publisher)

Get the list of posts sent by the publisher.

**Endpoint**:
GET /api/posts

**Response**:
{
"status": true,
"message": "OK.",
"data": [
{
"id": 4,
"slug": "the-best-body-wash-you-can-get",
"title": "The best body wash you can get",
"body": "Let me tell you what makes our body wash the best and most effective.",
"status": 0,
"topic_id": 1,
"user_id": 1,
"created_at": "2021-04-18T13:58:58.000000Z",
"updated_at": "2021-04-18T13:58:58.000000Z"
}
],
"error": null
}

## Publisher Server

### Setup

#### Codebase

- Clone the repo: https://github.com/ZestArinze/temp-publisher
- Connect to the database in .env file
- Run the commands:
  - composer install
  - php artisan key:generate
  - php artisan migrate
  - php artisan db:seed

### User Account

A user is needed for performing actions such as:

- Create new topics
- Add new posts

The artisan db:seed command above will create a user with email: john@doe.com and password: testing123 but you can also create another user by calling the following endpoint:

#### Create User

**Endpoint**:
\*POST /api/register

**Request Body:**
{
"name": "John Doe",
"email": "john@doe.com",
"password": "testing123",
"password_confirmation": "testing123"
}

**Response**:
{
"status": true,
"message": "Account created. You may login now.",
"data": {
"name": "John Doe",
"email": "john@doe.com",
"role": "User",
"updated_at": "2021-04-18T11:53:26.000000Z",
"created_at": "2021-04-18T11:53:26.000000Z",
"id": 1
},
"error": null
}

#### Login

**Endpoint**:
GET /api/login

**Request Body**:
{
"email": "john@doe.com",
"password": "testing123"
}

**Response**:
{
"status": true,
"message": "Login successful.",
"data": {
"auth_token": "1|zvDomPaHEeZKRMJXrPwAM0t8ZpnW64wcMXglEBu2",
"token_type": "Bearer token"
},
"error": null
}

Upon login, you get a Bearer token which you can pass to the header for endpoints that require authentication.

### Subscriber Credentials

When a notification is sent to a subscriber, the subscriber needs a way to know that the notification came from the publisher and not some random server out there.

The publisher can generate client id and client secret and give them to the subscriber.

**Note**: Whenever notification is sent to subscribers, the publisher sends along (in the request header) an HMAC of the client id computed using the client secret.

The subscriber upon getting the notification validates it using the same client id and client secret.

#### Generate Credentials

To generate credentials and hand them over to the subscriber, you need to pass the bearer token (returned upon successful login) in the Authorization header:

**Endpoint**:
GET /api/credentials

**Authorization Header**:
Bearer {token}

**Response**:
{
"status": true,
"message": "New client credentials generated successfully.",
"data": {
"client_id": "W0j8fMb0A7Huiz2K1618748058",
"client_secret": "HHXsVkIUq4MlmtLfkyz3YFEVge9kWPts"
},
"error": null
}

### Topics

This refers to the topics the subscribers can subscribe to. It is not what triggers the notification the subscribers get.

The artisan db:seed command above will create some topics but you can create more by calling the endpoint thus:

#### Create Topic

**Endpoint**:
POST /api/topics

**Authorization Header**:
Bearer {token}

**Request Body**:
{
"topic": "Body Wash"
}

**Response**:
{
"status": true,
"message": "Topic created.",
"data": {
"topic": "Body Wash",
"identifier": "body-wash",
"user_id": 1,
"updated_at": "2021-04-18T12:24:20.000000Z",
"created_at": "2021-04-18T12:24:20.000000Z",
"id": 1
},
"error": null
}

#### Get the list of topics

**Endpoint**:
GET /api/topics

**Response**:
{
"status": true,
"message": "OK.",
"data": [
{
"id": 1,
"topic": "Hair Cream",
"identifier": "hair-cream",
"user_id": 1,
"created_at": "2021-04-18T12:23:55.000000Z",
"updated_at": "2021-04-18T12:23:55.000000Z"
}
],
"error": null
}

#### Single Topic

**Endpoint**:
GET /api/topics/:id

**Response**:
{
"status": true,
"message": "Resource retrieved.",
"data": {
"id": 1,
"topic": "Hair Cream",
"identifier": "hair-cream",
"user_id": 1,
"created_at": "2021-04-18T12:23:55.000000Z",
"updated_at": "2021-04-18T12:23:55.000000Z"
},
"error": null
}

### Create Post & Send Notification

Notification is sent from the publisher to the subscribers when a new post is published.

Recall that the publisher will include an HMAC string of the client id computed with the client secret.

A post belongs to a topic. When a post is created, subscribers to the topic a notified.

#### Create Post

**Endpoint**:
POST /api/publish/:topicUniqueIdentifier

**Authorization Header**:
Bearer token

**Request Body**:
{
"title": "Glow with the best body wash",
"body": "Let me tell you what makes our body wash the best and most effective."
}

**Response**:
{
"status": true,
"message": "Post created.",
"data": {
"title": "Glow with the best body wash",
"body": "Let me tell you what makes our body wash the best and most effective.",
"topic_id": 2,
"slug": "glow-with-the-best-body-wash",
"user_id": 1,
"updated_at": "2021-04-18T12:59:27.000000Z",
"created_at": "2021-04-18T12:59:27.000000Z",
"id": 1
},
"error": null
}

### Notify Subscribers

Upon successful creation of a new post, an event is fired, the listeners are notified that a new post is created, a job to notify subscribers is dispatched.

THE END
