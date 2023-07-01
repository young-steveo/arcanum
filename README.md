Arcanum is under heavy development. v0.0.01

# Arcanum: A Cutting-Edge PHP Framework
Arcanum is a transformative PHP Framework with a fresh take on web application development, crafted meticulously with the modern software engineer in mind.

## Arcanum is Different
Unlike most PHP Frameworks, Arcanum applications don't follow the classic Model-View-Controller (MVC) paradigm.

MVC is a cornerstone of web development. It's simple. It encapsulates and separates the relationship between the data (Model) and the user interface (View), earning a reputation as the de facto architecture for small to mid-sized applications.

### MVC Fails Complex Apps
A tenet of "doing MVC right" is following the principle of "Skinny Controllers, Fat Models." This advice saves us from bloated controllers, but it inadvertently nudges us toward "God Models"—monolithic entities handling an array of responsibilities that span thousands of lines of code. As the complexity of an MVC application grows, so do these models, becoming increasingly difficult to maintain, test, and extend.

MVC offers little guidance on managing these "Fat Models," leading to sprawling codebases where one model serves too many masters, a tightly coupled system that hampers an application's ability to scale and evolve.

Highly complex apps demand a different approach.

### Command Query Responsibility Segregation
Arcanum applications follow the Command Query Responsibility Segregation (CQRS) pattern for a distinctive, robust, and scalable solution to building complex web applications. Operations that mutate state (Commands) are separated from those that read state (Queries), leading to a leaner, highly maintainable architecture. We're not trying to reinvent the wheel, but we're offering a different, perhaps more suitable tool for specific applications.

### Domain-Driven CQRS is a Game-Changer
Successful apps get bigger. Arcanum applications tackle this truth by utilizing clear bounded contexts. A bounded context represents a specific area of responsibility with its own ubiquitous language and model design. This separation simplifies the codebase, providing more evident areas of responsibility and making debugging and adding features a breeze.

## The Arcanum Philosophy
In Arcanum, we embrace CQRS and domain-driven design to tackle the complexity of modern web applications head-on. We're not trying to replace all the lovely MVC frameworks out there. We respect and appreciate their immense contributions. Instead, we're offering a novel perspective, a unique tool in your toolbox that can handle the intricacies of highly complex web applications differently.

With that spirit, welcome to Arcanum.
