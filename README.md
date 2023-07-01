Arcanum is under heavy development. v0.0.01

# Arcanum: A Cutting Edge PHP Framework

This is Arcanum, a ground-breaking PHP Framework that aims to stand shoulder to shoulder with Laravel, CakePHP, Laminas and Symfony. These well-established frameworks have carved a rightful, meaningful place in the developer universe, empowering engineers with robust Model-View-Controller (MVC) tools. We respect and appreciate their immense contributions.

## Arcanum is Different
The landscape of web development is ever-evolving, and with that comes new insights, new patterns, and new solutions. While MVC has proven to be an excellent pattern for many applications, we believe that highly complex applications demand a different approach. We're not trying to reinvent the wheel, but what we're offering is a different, perhaps more suitable tool for specific applications.

## MVC Fails Complex Apps
Model-View-Controller (MVC) is a cornerstone of web development. It's simple. It separates concerns. It elegantly encapsulates the relationship between the data (Model), user interface (View), and the operations that can be performed (Controller). There's a reason it has earned it a reputation as the de facto architecture for small to mid-sized applications.

A tenet of "doing MVC right" is following the principle of "Skinny Controllers, Fat Models." This advice saves us from bloated controllers, but it inadvertently nudges us toward "God Models"—monolithic entities handling an array of responsibilities that span thousands of lines of code. As the complexity of an MVC application grows, so do these models, becoming increasingly difficult to maintain, test, and extend.

MVC offers no guidance on managing these "Fat Models," leading to sprawling codebases where one model serves too many masters, a tightly coupled system that hampers an application's ability to scale and evolve.

## Domain-Driven CQRS is a Game-Changer
Enter Command Query Responsibility Segregation (CQRS), a design pattern that turns the traditional web-dev approach on its head. Instead of centralizing command and query responsibilities in one monolithic model, CQRS separates these concerns, leading to cleaner, more maintainable, and more flexible codebases.

CQRS aligns well with the concept of bounded contexts–specific areas of responsibility within a system that have their own ubiquitous language and models. Each bounded context has it's own commands and queries, ensuring these models remain focused, lean, and entirely separate from one another.

This separation simplifies the codebase, providing clearer areas of responsibility, and making it easier to debug, understand, and extend the system.

# The Arcanum Philosophy

In Arcanum, we embrace CQRS and DDD to tackle the complexity of modern web applications head-on. We understand the pitfalls of MVC in large-scale applications and empower developers to break free from monolithic models and tightly coupled systems.

We believe the future of application architecture lies not in a single pattern, but in the appropriate selection and implementation of the right pattern for the job. Arcanum is designed to help developers navigate this landscape, offering a modern, flexible approach to complex applications.

We're not trying to replace Laravel or Symfony or CakePHP, or any other mostly-mvc-framework. Instead, we're offering a novel perspective, a unique tool in your toolbox that can handle the intricacies of highly complex web applications differently.

With that spirit, welcome to Arcanum. 

