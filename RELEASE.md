#Releases

## 0.2.2

- Home page now displays README instead of Hello world
- Adds Skeleton boilerplate

## 0.2.1

- Adds example of $fiddle->play() compared to $fiddle->export
- Fiddle start and end (setup of html layout) is now in the routes instead of having to set them in the fiddles. This prepares the code to be decoupled from the default layout.
- Chains configuration to Fiddle object
- Initializes $fiddle object from index instead of fiddle script

## 0.2.0

- Fixed trailing 1 in html output
- MAJOR CHANGE: Turned Fiddle class into a singleton, breaking existing fiddles and examples

## 0.1.1

- Removed an old unrelated directory `other/`
- Added a new directory `fiddles/` and the route of the same name to put custom fiddles in a safe place

## 0.1.0

- First pre-release to include a microframework (Silex) and namespacing. Before that, phpfiddler was a QAD script that growed organically.