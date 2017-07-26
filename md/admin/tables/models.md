# Models

## Metadata

| Field | Value type | Description |
|---|---|---|
| `currentPage` | `Integer` | Página actual. |
| `lastPage` | `Integer` | Última página. |
| `itemsPerPage` | `Integer` | Cantidad de resultados mostrados por página. |
| `totalItems` | `Integer` | Cantidad total de resultados. |

### Example

```json
{
  "currentPage": 1,
  "lastPage": 3,
  "itemsPerPage": 20,
  "totalItems": 54
}
```

## Order

| Field | Value type | Description |
|---|---|---|
| *(†)* | *(‡)* | Texto que se quiere buscar. |

* † Mismo nombre que la columna en la base de datos.
* ‡ `1` para ascendente, o `-1` para descendente.

### Example

```json
{
  "id": 1,
  "public_id": -1
}
```
