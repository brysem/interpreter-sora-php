# Sora (PHP Interpreter)
Experimenting with an interpreter written in PHP.

## Abstract Syntax Tree Dot File Generation
You can generate a dot file for graphviz, creating the file `ast.dot`.
```bash
./sora -d ./examples/variable_assignment.pp > ast.dot
```

To generate an image right away you can pipe the output to a file.
```bash
./sora -d ./examples/variable_assignment.pp > ast.dot && dot -Tpng -o ast.png ast.dot
```
