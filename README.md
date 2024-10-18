# Elgentos_HyvaCROPurchasingConfidence

Magento 2 Hyva CRO extension for increasing the purchasing confidence by adding a "Customers usually keep this item" block when the item has been returned less than a configured threshold return percentage.

![image](https://github.com/user-attachments/assets/83531365-f0c7-4318-80aa-735767d6c36a)

## Install

```
composer require elgentos/magento2-hyva-cro-purchasing-confidence
bin/magento set:up
```

## Configuration

You initially want to run the calculation crons manually, so you have that data. The updating of the return percentage on a product basis happens monthly, the recalculation of the average return percentage happens weekly.

```
magerun2 sys:cron:run hyva_cro_purchasing_confidence_update_product_return_percentage
magerun2 sys:cron:run hyva_cro_purchasing_confidence_update_average_return_percentage
```

![image](https://github.com/user-attachments/assets/be21c4e0-cd83-4ccb-8bfb-45d9660134ad)
