# Yii2 Database procedure extension
This extension provides layer for working with database procedures/views in Yii2 application. 

It is created to work well with Yii2 applications, but with right configuration
it could be used with other frameworks too.

### REQUIREMENTS

The minimum requirement by this project: 
- PHP 7.2.0.

### Procedures
In Application every procedure have its
own model. Procedure model must implement interface `damidev\dbprocedures\models\IProcedure`. For easier work with procedures, we
created abstract class `damidev\dbprocedures\models\Procedure`. This class implements `IProcedure`, calls events, defines scenarios.

Every procedure model must define `Procedure::call()` and at least call method `Procedure::execute()`. Its becouse procedures
returns single or many objects, this information know only programmer. 

So for single result procedure we implement `damidev\dbprocedures\models\SimpleProcedure`.
For multiple rows we implement `damidev\dbprocedures\models\MultiProcedure`. 
There is `damidev\dbprocedures\models\PagnatedProcedure` that provides pagination logic on `MultiProcedure`.

#### Procedure lifecycle

When `Procedure::call()` is invoked

```
-> call()
-> call execute(queryOne|queryAll)
    -> set scenario to Procedure::PROCEDURE_CALL
    -> call event beforeCall()               -- used to modify (or validate) input attributes
    -> db procedure exec
    -> set scenario to previous one
    -> call event afterCall($result)         -- used to modify procedure result
<- $result
```

When `Procedure::callCount()` is invoked

```
-> callCount()
    -> set scenario to Procedure::PROCEDURE_COUNT
    -> db count procedure exec
    -> set scenario to previous one
<- $count
```

### Procedure actions
Extension provides ready-to-use standalone actions, that makes creating for example REST API lot of easier. 

There is `damidev\dbprocedures\actions\SimpleProcedureAction` for view/detail actions and 
`damidev\dbprocedures\actions\PaginatedProcedureAction` for index/list/paginated actions.