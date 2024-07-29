(() => {
    $(document).ready(() => {
        'use strict'
        const root = document.querySelector('.bookmarks')

        if (!root) {
            return;
        }

        root.querySelectorAll('.create-bookmark').forEach(el => el.addEventListener('click', event => {
            event.preventDefault()
            event.stopPropagation()

            root.querySelector('form').classList.remove('hidden')
        }))

        root.querySelectorAll('form').forEach(el => el.addEventListener('submit', event => {
            event.preventDefault()
            event.stopPropagation()

            const form = event.currentTarget
            const nameInput = form.querySelector('input[name="bookmark[name]"]')
            const routeInput = form.querySelector('input[name="bookmark[route]"]')
            const routeParamsInput = form.querySelector('input[name="bookmark[route_params]"]')

            const request = new XMLHttpRequest()
            request.open('POST', event.currentTarget.action, true)
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
            request.onreadystatechange = event => {
                if (XMLHttpRequest.DONE === event.currentTarget.readyState) {
                    form.querySelector('button[type="submit"]').disabled = false

                    const response = JSON.parse(event.currentTarget.response)
                    if (201 === event.currentTarget.status) {
                        form.classList.add('hidden')
                        form.querySelectorAll('.has-error').forEach(element => {
                            element.classList.remove('has-error')
                        })
                        form.querySelectorAll('.help-block').forEach(element => {
                            element.remove()
                        })
                        nameInput.value = ''
                        const link = document.createElement('a')
                        link.href = response.url
                        link.textContent = response.name

                        const item = document.createElement('li')
                        item.appendChild(link)

                        root.querySelector('.bookmarks-list li:last-child').before(item)
                        root.querySelector('.badge').textContent = (parseInt(root.querySelector('.badge').textContent) + 1).toString()

                        return
                    }

                    if (response.hasOwnProperty('violations')) {
                        const violationsContainers = {}
                        response.violations.forEach(violation => {
                            if (!violation.hasOwnProperty('propertyPath')) {
                                return
                            }

                            const existingViolation = document.querySelector(`li[data-property="${violation.propertyPath}"][data-title="${violation.title}"]`);

                            if (existingViolation) {
                                return;
                            }

                            if (!violationsContainers.hasOwnProperty(violation.propertyPath)) {
                                const list = document.createElement('ul')
                                const root = document.createElement('div')
                                root.classList.add('help-block')
                                root.appendChild(list)

                                violationsContainers[violation.propertyPath] = {
                                    root: root,
                                    list: list,
                                }
                            }

                            const violationItem = document.createElement('li')
                            violationItem.textContent = violation.title
                            violationItem.setAttribute('data-property', violation.propertyPath);
                            violationItem.setAttribute('data-title', violation.title);

                            violationsContainers[violation.propertyPath].list.appendChild(violationItem)
                        })

                        Object.entries(violationsContainers).forEach(([name, element]) => {
                            const formGroup = form.querySelector(`[name="bookmark[${name}]"]`).parentNode
                            formGroup.classList.add('has-error')
                            formGroup.appendChild(element.root)
                        })
                    }
                }
            }

            request.send(`name=${nameInput.value}&route=${routeInput.value}${routeParamsInput.value}`)
        }))
    })
})();
