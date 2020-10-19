---
title: Push environment files
order: 3
---

# Push environment files

Once you have pulled down your environment file using `forge env:pull`, you can push the changes back to Laravel Forge by calling:

`forge env:push`

To ensure that you do not accidentally keep an old state of your environment file, Forge CLI asks you if you want to delete the env file after pushing.
