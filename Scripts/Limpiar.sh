
#!/bin/bash
awk '$3 !~ "\*"' $1 > $2
