# Wikibase Export REST API

## Export

Route: `/wikibase-export/v0/export`

Method: `GET`

Example for exporting items Q1, Q2, Q3 with properties P1, P2 for the period of 2021-2022:

```shell
curl "http://localhost:8484/rest.php/wikibase-export/v0/export?subject_ids=Q1|Q2|Q3&grouped_statement_property_ids=P1|P2&ungrouped_statement_property_ids=P3|P4&start_year=2021&end_year=2022" -o export.csv
```

### Request parameters

**Query**

| parameter                          | required | example                              | description                         |
|------------------------------------|----------|--------------------------------------|-------------------------------------|
| `subject_ids`                      | yes      | `Q1\|Q2\|Q3` for Items Q1, Q2 and Q3 | The item IDs, separated with \|     |
| `grouped_statement_property_ids`   | yes      | `P1\|P2` for Properties P1 and P2    | The property IDs, separated with \| |
| `ungrouped_statement_property_ids` | yes      | `P1\|P2` for Properties P1 and P2    | The property IDs, separated with \| |
| `start_year`                       | yes      | `2021`                               | The start year                      |
| `end_year`                         | yes      | `2022`                               | The end year                        |
