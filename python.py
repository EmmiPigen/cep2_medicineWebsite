import requests

url = "http://forglemmigej.duckdns.org/api/test"

myobj = {"name": "test", "age": 20}

x = requests.post(url, json=myobj)
print(x.text)