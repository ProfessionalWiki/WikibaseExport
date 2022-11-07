# Wikibase Export REST API

## Export

Route: `/wikibase-export/v0/export`

Method: `GET`

Example for exporting items Q1, Q2, Q3 with properties P1, P2 in wide CSV format:

```shell
curl "http://localhost:8484/rest.php/wikibase-export/v0/export?subject_ids=Q1|Q2|Q3&statement_property_ids=P1|P2&format=csvwide"
```

### Request parameters

**Query**

| parameter                | required | example                                      | description                             |
|--------------------------|----------|----------------------------------------------|-----------------------------------------|
| `subject_ids`            | yes      | "Q1&#124;Q2&#124;Q3" for Items Q1, Q2 and Q3 | The item IDs, separated with &#124;     |
| `statement_property_ids` | yes      | "P1&#124;P2" for Properties P1 and P2        | The property IDs, separated with &#124; |
| `start_time`             | no       | 2021                                         | TODO                                    |
| `end_time`               | no       | 2022                                         | TODO                                    |
| `format`                 | no       | TODO                                         | TODO                                    |
