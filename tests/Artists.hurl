# List
GET {{host}}/artists
HTTP 200
[Asserts]
jsonpath "$.artists" count > 1

# Search
GET {{host}}/artists?s=Queen
HTTP 200
[Asserts]
jsonpath "$.artists[0].Name" == "Queen"

# By ID
GET {{host}}/artists/51
HTTP 200
[Asserts]
jsonpath "$.artists[0].Name" == "Queen"

# POST
POST {{host}}/artists
Content-Type: application/json
{
    "name": "Shu-Bi-Dua"
}
HTTP 201
[Asserts]
jsonpath "$.artists[0].Name" == "Shu-Bi-Dua"
[Captures]
shu_bi_dua_id: jsonpath "$.artists[0].Name"

# DELETE
DELETE {{host}}/artists/{{shu_bi_dua_id}}
HTTP 200