# Request

| Method | Route | Path |
|---|---|---|
| **POST** | | |

## Headers

| Name | Value |
|---|---|
| `Accept` | `application/json` |
| `Content-Type` | `application/json` |

## Body

| Field | Value type | Optional? | Description |
|---|---|---|---|
| `order` | `Order` | **Y** | Orden de las columnas. |
| `page` | `Integer` | | Página de resultados que se quiere obtener. |
| `query` | `String` | **Y** | Texto que se quiere buscar. |

## Example

```
POST / HTTP/1.1
Accept: application/json
Content-Type: application/json
```

```json
{
  "order": {
    "id": 1,
    "public_id": -1
  },
  "page": 1,
  "query": "Lorem ipsum"
}
```
