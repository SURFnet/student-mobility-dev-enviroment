#!/bin/bash
LINE="127.0.0.1 intekenontvanger-generiek home-institution-mock inteken-ontvanger-email brokerclient brokerserver mock-backend"

# Check if the line already exists in /etc/hosts
grep -Fxq "$LINE" /etc/hosts

# If the line is not found, add it
if [ $? -ne 0 ]; then
    echo "$LINE" | sudo tee -a /etc/hosts > /dev/null
    echo "Entry added to /etc/hosts."
else
    echo "Entry already exists in /etc/hosts."
fi

#!/bin/bash

if command -v docker &> /dev/null; then
    docker compose up
else
    echo "Docker is not installed."
    exit 1
fi


