# Wikibase Export REST API

## Export

Route: `/wikibase-export/v0/export`

Method: `GET`

Example for exporting items Q1, Q2, Q3 with properties P1, P2 (grouped by year) and P3, P4 (ungrouped) for the period
of 2021-2022 and with property labels used for headers:

```shell
curl -G "http://localhost:8484/rest.php/wikibase-export/v0/export" \
     -d "subject_ids=Q1|Q2|Q3" \
     -d "grouped_statement_property_ids=P1|P2" \
     -d "ungrouped_statement_property_ids=P3|P4" \
     -d "start_year=2021" \
     -d "end_year=2022" \
     -d "header_type=label" \
     -d "language=en" \
     -o export.csv
```

### Request parameters

**Query**

| parameter                          | required | default | example                              | description                                                       |
|------------------------------------|----------|---------|--------------------------------------|-------------------------------------------------------------------|
| `subject_ids`                      | yes      |         | `Q1\|Q2\|Q3` for Items Q1, Q2 and Q3 | The item IDs, separated with \|                                   |
| `grouped_statement_property_ids`   | no       | `[]`    | `P1\|P2` for Properties P1 and P2    | The property IDs of statements grouped by year, separated with \| |
| `ungrouped_statement_property_ids` | no       | `[]`    | `P1\|P2` for Properties P2 and P3    | The property IDs of ungrouped statements, separated with \|       |
| `start_year`                       | no       |         | `2021`                               | The start year                                                    |
| `end_year`                         | no       |         | `2022`                               | The end year                                                      |
| `header_type`                      | no       | `id`    | `id` or `label`                      | Whether to use property IDs or labels for the headers             |
| `language`                         | no       | `null`  | `en`                                 | The export language (if defined in config)                        |

When specifying `grouped_statement_property_ids` you need to include `start_year` and `end_year`.

If neither `grouped_statement_property_ids` nor `ungrouped_statement_property_ids` is specified then the export will
contain only item IDs and labels.
