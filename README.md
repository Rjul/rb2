# rules
## server cmd
```bash
git pull && sudo -u www-data php artisan cache:clear  && npm run build

```
## Installation
```bash
composer install && npm install && ./vendor/bin/sail up 
```
```bash
npm run dev
```
```bash
sail artisan migrate:fresh && sail artisan db:seed
```
### (re)generer un APP_KEY
```bash
sail artisan artisan key:generate && sail artisan config:cache
```

## Roles
"platform.group.programme" => administration of programme's groups

"platform.programmes" => administration of all programmes and emisions

"platform.emissions.{ $emisions->id }" => administration of this emissions


### programme

- Posibilité d'archiver un programme
### log-viewer
http://localhost/admin/main/log-viewer

### OLD traqueur
```html

<script>
document.addEventListener('DOMContentLoaded', () => {
    console.log('ready')
    let alreadySend = false;
    let intervalCallback = setTimeout(classBind, 500);
    function classBind(){
        Array.from(document.getElementsByClassName('active-demain-btn')).forEach(elms => {
            elms.addEventListener('click', (e) => {
                if (!alreadySend){
                    let formData = new FormData();
                    formData.append('event_name', 'active-demain-btn');
                    formData.append('event_id', 2);
                    formData.append('name_target', 'demain');
                    formData.append('id_target', 1);
                    response = fetch("/api/statistique", {
                        body: formData,
                        headers: {
                            "Content-Type": "active-demain-btn",
                            'event-name': 'click_play',
                            'event-id': 2,
                            'name-target': 'demain',
                            'id-target': 1,
                        },
                        method: "post",
                    }).then(function(response){
                        alreadySend = true;
                    });
                }
            })
            clearInterval(intervalCallback);
        })
        }

    })

</script>
```
