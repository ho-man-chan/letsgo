import pusher

pusher_client = pusher.Pusher(
  app_id='658163',
  key='0804ef5e4cd344d9d174',
  secret='d761f68cce84804f0ccc',
  cluster='us2',
  ssl=True
)

pusher_client.trigger('my-channel', 'my-event', {'message': 'hello world'})