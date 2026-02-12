# Roadmap
- Cache responses from APIs to improve speed
- Provide summary output table of libraries used the most across your projects
- HTTP Pooling to increase speed of interacting with Composer API

# Tech Debt
- Tests with a dependency on HTTP requests (integration tests take 20+ minutes)
- Lack of clarity around filesystem - hardcoding and assumptions tied to my local disk

# Curiosities (Research)
- NPM API improvements - replication API, Pooling (this is the bulk of program run time)
- Plugins for other languages (i.e. how to better abstract?)
