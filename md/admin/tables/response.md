# Response

| Status |
|---|
| **200** |

## Headers

| Name | Value |
|---|---|
| `Content-Type` | `application/json` |

## Body

| Field | Value type | Description |
|---|---|---|
| `meta` | `Metadata` | Metadata. |
| `items` | `Array<Object>` | Array con los resultados de búsqueda. |

## Example

```
HTTP/1.1 200 OK
Content-Type: application/json
```

```json
{
  "meta": {
    "currentPage": 1,
    "lastPage": 3,
    "itemsPerPage": 20,
    "totalItems": 54
  },
  "items" [
    {
      "id": "1",
      "public_id": "NAV-0001"
    }
  ]
}
```
