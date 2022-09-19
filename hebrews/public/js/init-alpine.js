document.addEventListener('alpine:init', () => {
    Alpine.data('data', () => ({
        isProfileMenuOpen: false,
        toggleProfileMenu() {
            this.isProfileMenuOpen = !this.isProfileMenuOpen
        },

        closeProfileMenu() {
            this.isProfileMenuOpen = false
        },

        isSideMenuOpen: false,
        toggleSideMenu() {
            this.isSideMenuOpen = !this.isSideMenuOpen
        },

        closeSideMenu() {
            this.isSideMenuOpen = false
        },

        isMultiLevelMenuOpen: false,
        toggleMultiLevelMenu() {
            this.isMultiLevelMenuOpen = !this.isMultiLevelMenuOpen
        },

        // Modal
        isModalOpen: false,
        isConfirmModalOpen: false,
        isDeleteModalOpen: false,
        isSearchModalOpen: false,
        isCancelModalOpen: false,


        trapCleanup: null,
        openModal() {
            this.isModalOpen = true
            this.trapCleanup = focusTrap(document.querySelector('#modal'))
        },
        openConfirmModal() {
            this.isConfirmModalOpen = true
            this.trapCleanup = focusTrap(document.querySelector('#confirm-modal'))
        },
        openDeleteModal() {
            this.isDeleteModalOpen = true
            this.trapCleanup = focusTrap(document.querySelector('#delete-modal'))
        },
        openSearchModal() {
            this.isSearchModalOpen = true
            this.trapCleanup = focusTrap(document.querySelector('#search-modal'))
        },
        openCancelModal() {
            this.isCancelModalOpen = true
            this.trapCleanup = focusTrap(document.querySelector('#cancel-modal'))
        },
        closeModal() {
            this.isModalOpen = false
            this.isConfirmModalOpen = false
            this.isDeleteModalOpen = false
            this.isSearchModalOpen = false
            this.isCancelModalOpen = false

            this.trapCleanup()
        },

        //Temporarily add Menu Item
        loading: false,
        orderType: 'dinein',
        itemQuantity: 1,
        orderItems: [],
        subTotalPrice: 0,

        //Cofirm Order
        isDinein: true,
        orderErrorMessage: null,
        transSelectedTables: [],
        transDiscount: 'none',
        transNote: '',
        showErrorMessage: false,
        errorMessage: '',
        showSuccessMessage: false,
        successMessage: '',

        addOrderItem(itemToSave,from) {
            if (this.isDinein) {
                let foundIndex = this.orderItems.findIndex(element => element.menu_id == itemToSave.id && element.type == 'dinein');

                if (foundIndex != -1) {
                    // if the item is already in the array update quantity and price
                    this.orderItems[foundIndex].qty = parseInt(this.orderItems[foundIndex].qty) + 1
                    this.orderItems[foundIndex].total_price = parseFloat(this.orderItems[foundIndex].total_price) + parseFloat(itemToSave.dinein_price)
                } else {
                    this.orderItems.push({
                        'ord_item_id' : itemToSave.id+Date.now(),
                        'menu_id' : itemToSave.id,
                        'name' : itemToSave.name,
                        'from': from,
                        'type' : 'dinein',
                        'is_dinein' : true,
                        'dinein_price' : parseFloat(itemToSave.dinein_price),
                        'takeout_price' : parseFloat(itemToSave.takeout_price),
                        'qty' : 1,
                        'total_price' : parseFloat(itemToSave.dinein_price),
                    })
                }
                // Update subtotal price
                this.subTotalPrice = (parseFloat(this.subTotalPrice) + parseFloat(itemToSave.dinein_price)).toFixed(2)
            } else {
                let foundIndex = this.orderItems.findIndex(element => element.menu_id == itemToSave.id && element.type == 'takeout');

                if (foundIndex != -1) {
                    // if the item is already in the array update quantity and price
                    this.orderItems[foundIndex].qty = parseInt(this.orderItems[foundIndex].qty) + 1
                    this.orderItems[foundIndex].total_price = parseFloat(this.orderItems[foundIndex].total_price) + parseFloat(itemToSave.takeout_price)
                } else {
                    this.orderItems.push({
                        'ord_item_id' : itemToSave.id+Date.now(),
                        'menu_id' : itemToSave.id,
                        'name' : itemToSave.name,
                        'from': from,
                        'type' : 'takeout',
                        'is_dinein' : false,
                        'dinein_price' : parseFloat(itemToSave.dinein_price),
                        'takeout_price' : parseFloat(itemToSave.takeout_price),
                        'qty' : 1,
                        'total_price' : parseFloat(itemToSave.takeout_price),
                    })
                }
                // Update subtotal price
                this.subTotalPrice = (parseFloat(this.subTotalPrice) + parseFloat(itemToSave.takeout_price)).toFixed(2)
            }
        },

        removeOrderItem(id, tot_price) {
            // Subtract total item price to the subtotal price
            this.subTotalPrice = (parseFloat(this.subTotalPrice) - parseFloat(tot_price)).toFixed(2)

            // Remove item from the list
            this.orderItems = this.orderItems.filter (item =>
                item.ord_item_id != id
            )
        },

        resetOrders() {
            this.orderItems = []
            this.subTotalPrice = 0
            this.transSelectedTables = []
            this.transDiscount = 'none'
            this.transNote = ''
        },

        changeOrderType(change_ord_item) {

            if(change_ord_item.type == 'dinein') {
                //find the order item id
                let foundIndex = this.orderItems.findIndex(element => element.ord_item_id == change_ord_item.ord_item_id);
                if (foundIndex != -1) {

                    // re-calculate total amount and subtotal
                    let lessOldPrice = parseFloat(this.subTotalPrice) - parseFloat(change_ord_item.total_price)
                    this.subTotalPrice = (lessOldPrice + (parseFloat(change_ord_item.takeout_price) * parseFloat(change_ord_item.qty))).toFixed(2)

                    // if the item is already in the array update quantity, price and type
                    this.orderItems[foundIndex].type = 'takeout'
                    this.orderItems[foundIndex].is_dinein = false
                    this.orderItems[foundIndex].total_price = parseFloat(change_ord_item.takeout_price) * parseFloat(change_ord_item.qty)

                }
            } else {
                let foundIndex = this.orderItems.findIndex(element => element.ord_item_id == change_ord_item.ord_item_id);
                if (foundIndex != -1) {
                    // re-calculate total amount and subtotal
                    let lessOldPrice = parseFloat(this.subTotalPrice) - parseFloat(change_ord_item.total_price)
                    this.subTotalPrice = (lessOldPrice + (parseFloat(change_ord_item.dinein_price) * parseFloat(change_ord_item.qty))).toFixed(2)

                    // if the item is already in the array update quantity, price and type
                    this.orderItems[foundIndex].type = 'dinein'
                    this.orderItems[foundIndex].is_dinein = true
                    this.orderItems[foundIndex].total_price = parseFloat(change_ord_item.dinein_price) * parseFloat(change_ord_item.qty)

                }
            }
        },

        async confirmOrder (url, token) {
            this.loading = true
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": token
                },
                body: JSON.stringify({
                    items : this.orderItems,
                    tables : this.transSelectedTables,
                    discount_type : this.transDiscount,
                    note : this.transNote,
                    items : this.orderItems,
                    subtotal : this.subTotalPrice,
                })
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false
                //Close modal
                this.closeModal()
                if (data.status == 'success') {
                    alert(data.message)
                    window.location.replace(data.redirect);
                } else {
                    this.showErrorMessage = true
                    this.errorMessage = data.message
                }
            }).catch (e => {
                this.loading = false
                //Close modal
                alert('Unable to connect, please try again.')
                this.closeModal()

            })
        },

        // MENU SECTION
        deleteCategoryId: null,

        // InventorySection
        updateInventoryId: null,
        inventoryStock: 0,

        // USER SECTION
        deleteUserId: null,
        resetUserId: null,

        // KITCHEN SECTION
        deleteKitchenItemId: null,
        deleteKitchenItemName: '',
        completeKitchenItemId: null,
        completeKitchenItemName: '',
        completeKitchenOrderId: null,

        // BAR SECTION
        completeBarItemId: null,
        completeBarItemName: '',

        // DISPATCH SECTION
        serveKitchenItemId: null,
        serveKitchenItemName: '',


        //Pay order
        pay(key) {
            console.log(key)
        }


    }))
    Alpine.data('data', () => ({
        isProfileMenuOpen: false,
        toggleProfileMenu() {
            this.isProfileMenuOpen = !this.isProfileMenuOpen
        },

        closeProfileMenu() {
            this.isProfileMenuOpen = false
        },

        isSideMenuOpen: false,
        toggleSideMenu() {
            this.isSideMenuOpen = !this.isSideMenuOpen
        },

        closeSideMenu() {
            this.isSideMenuOpen = false
        },

        isMultiLevelMenuOpen: false,
        toggleMultiLevelMenu() {
            this.isMultiLevelMenuOpen = !this.isMultiLevelMenuOpen
        },

        // Modal
        isModalOpen: false,
        isConfirmModalOpen: false,
        isDeleteModalOpen: false,
        isUpdateModalOpen: false,
        isSearchModalOpen: false,
        isCancelModalOpen: false,


        trapCleanup: null,
        openModal() {
            this.isModalOpen = true
            this.trapCleanup = focusTrap(document.querySelector('#modal'))
        },
        openConfirmModal() {
            this.isConfirmModalOpen = true
            this.trapCleanup = focusTrap(document.querySelector('#confirm-modal'))
        },
        openDeleteModal() {
            this.isDeleteModalOpen = true
            this.trapCleanup = focusTrap(document.querySelector('#delete-modal'))
        },
        openUpdateModal() {
            this.isUpdateModalOpen = true
            this.trapCleanup = focusTrap(document.querySelector('#update-modal'))
        },
        openSearchModal() {
            this.isSearchModalOpen = true
            this.trapCleanup = focusTrap(document.querySelector('#search-modal'))
        },
        openCancelModal() {
            this.isCancelModalOpen = true
            this.trapCleanup = focusTrap(document.querySelector('#cancel-modal'))
        },
        closeModal() {
            this.isModalOpen = false
            this.isConfirmModalOpen = false
            this.isUpdateModalOpen = false
            this.isDeleteModalOpen = false
            this.isSearchModalOpen = false
            this.isCancelModalOpen = false

            this.trapCleanup()
        },

        //Temporarily add Menu Item
        loading: false,
        orderType: 'dinein',
        itemQuantity: 1,
        orderItems: [],
        subTotalPrice: 0,

        //Cofirm Order
        isDinein: true,
        orderErrorMessage: null,
        transSelectedTables: [],
        transDiscount: 'none',
        transNote: '',
        showErrorMessage: false,
        errorMessage: '',
        showSuccessMessage: false,
        successMessage: '',

        addOrderItem(itemToSave,from) {
            if (this.isDinein) {
                let foundIndex = this.orderItems.findIndex(element => element.menu_id == itemToSave.id && element.type == 'dinein');

                if (foundIndex != -1) {
                    // if the item is already in the array update quantity and price
                    this.orderItems[foundIndex].qty = parseInt(this.orderItems[foundIndex].qty) + 1
                    this.orderItems[foundIndex].total_price = parseFloat(this.orderItems[foundIndex].total_price) + parseFloat(itemToSave.dinein_price)
                } else {
                    this.orderItems.push({
                        'ord_item_id' : itemToSave.id+Date.now(),
                        'menu_id' : itemToSave.id,
                        'name' : itemToSave.name,
                        'from': from,
                        'type' : 'dinein',
                        'is_dinein' : true,
                        'dinein_price' : parseFloat(itemToSave.dinein_price),
                        'takeout_price' : parseFloat(itemToSave.takeout_price),
                        'qty' : 1,
                        'total_price' : parseFloat(itemToSave.dinein_price),
                    })
                }
                // Update subtotal price
                this.subTotalPrice = (parseFloat(this.subTotalPrice) + parseFloat(itemToSave.dinein_price)).toFixed(2)
            } else {
                let foundIndex = this.orderItems.findIndex(element => element.menu_id == itemToSave.id && element.type == 'takeout');

                if (foundIndex != -1) {
                    // if the item is already in the array update quantity and price
                    this.orderItems[foundIndex].qty = parseInt(this.orderItems[foundIndex].qty) + 1
                    this.orderItems[foundIndex].total_price = parseFloat(this.orderItems[foundIndex].total_price) + parseFloat(itemToSave.takeout_price)
                } else {
                    this.orderItems.push({
                        'ord_item_id' : itemToSave.id+Date.now(),
                        'menu_id' : itemToSave.id,
                        'name' : itemToSave.name,
                        'from': from,
                        'type' : 'takeout',
                        'is_dinein' : false,
                        'dinein_price' : parseFloat(itemToSave.dinein_price),
                        'takeout_price' : parseFloat(itemToSave.takeout_price),
                        'qty' : 1,
                        'total_price' : parseFloat(itemToSave.takeout_price),
                    })
                }
                // Update subtotal price
                this.subTotalPrice = (parseFloat(this.subTotalPrice) + parseFloat(itemToSave.takeout_price)).toFixed(2)
            }
        },

        removeOrderItem(id, tot_price) {
            // Subtract total item price to the subtotal price
            this.subTotalPrice = (parseFloat(this.subTotalPrice) - parseFloat(tot_price)).toFixed(2)

            // Remove item from the list
            this.orderItems = this.orderItems.filter (item =>
                item.ord_item_id != id
            )
        },

        resetOrders() {
            this.orderItems = []
            this.subTotalPrice = 0
            this.transSelectedTables = []
            this.transDiscount = 'none'
            this.transNote = ''
        },

        changeOrderType(change_ord_item) {

            if(change_ord_item.type == 'dinein') {
                //find the order item id
                let foundIndex = this.orderItems.findIndex(element => element.ord_item_id == change_ord_item.ord_item_id);
                if (foundIndex != -1) {

                    // re-calculate total amount and subtotal
                    let lessOldPrice = parseFloat(this.subTotalPrice) - parseFloat(change_ord_item.total_price)
                    this.subTotalPrice = (lessOldPrice + (parseFloat(change_ord_item.takeout_price) * parseFloat(change_ord_item.qty))).toFixed(2)

                    // if the item is already in the array update quantity, price and type
                    this.orderItems[foundIndex].type = 'takeout'
                    this.orderItems[foundIndex].is_dinein = false
                    this.orderItems[foundIndex].total_price = parseFloat(change_ord_item.takeout_price) * parseFloat(change_ord_item.qty)

                }
            } else {
                let foundIndex = this.orderItems.findIndex(element => element.ord_item_id == change_ord_item.ord_item_id);
                if (foundIndex != -1) {
                    // re-calculate total amount and subtotal
                    let lessOldPrice = parseFloat(this.subTotalPrice) - parseFloat(change_ord_item.total_price)
                    this.subTotalPrice = (lessOldPrice + (parseFloat(change_ord_item.dinein_price) * parseFloat(change_ord_item.qty))).toFixed(2)

                    // if the item is already in the array update quantity, price and type
                    this.orderItems[foundIndex].type = 'dinein'
                    this.orderItems[foundIndex].is_dinein = true
                    this.orderItems[foundIndex].total_price = parseFloat(change_ord_item.dinein_price) * parseFloat(change_ord_item.qty)

                }
            }
        },

        async confirmOrder (url, token) {
            this.loading = true
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": token
                },
                body: JSON.stringify({
                    items : this.orderItems,
                    tables : this.transSelectedTables,
                    discount_type : this.transDiscount,
                    note : this.transNote,
                    items : this.orderItems,
                    subtotal : this.subTotalPrice,
                })
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false
                //Close modal
                this.closeModal()
                if (data.status == 'success') {
                    alert(data.message)
                    window.location.replace(data.redirect);
                } else {
                    this.showErrorMessage = true
                    this.errorMessage = data.message
                }
            }).catch (e => {
                this.loading = false
                //Close modal
                alert('Unable to connect, please try again.')
                this.closeModal()

            })
        },

        // MENU SECTION
        deleteCategoryId: null,
        deleteMenuId: null,

        // Inventory Section
        updateInventoryId: null,
        updateInventoryName: null,
        updateInventoryUnit: null,
        updateInventoryStock: 0,

        //Cart Section
        updateCartId: null,
        updateCartItemQty: 0,
        cartItemNote: null,
        deleteCartItemId: null,

        // Edit Order Item Section
        updateOrderItemId: null,
        updateOrderItemName: '',
        updateOrderItemQty: 0,
        deleteOrderItemId: null,

        // USER SECTION
        deleteUserId: null,
        resetUserId: null,

        // KITCHEN SECTION
        deleteKitchenItemId: null,
        deleteKitchenItemName: '',
        completeKitchenItemId: null,
        completeKitchenItemName: '',
        completeKitchenOrderId: null,

        // BAR SECTION
        completeBarItemId: null,
        completeBarItemName: '',

        // DISPATCH SECTION
        serveKitchenItemId: null,
        serveKitchenItemName: '',


        //Pay order
        pay(key) {
            console.log(key)
        }


    }))
})
