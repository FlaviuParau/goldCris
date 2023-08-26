<?php

class Blugento_Pdf_Model_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer
{
    /**
     * Send all emails from email list
     *
     * @return Mage_Core_Model_Email_Template_Mailer
     */
    public function send()
    {
        /** @var $helper Blugento_Pdf_Helper_Data */
        $helper = Mage::helper('blugento_pdf');

        /** @var $emailTemplate Mage_Core_Model_Email_Template */
        $emailTemplate = Mage::getModel('core/email_template');

        if (Mage::getStoreConfig('sales_pdf/invoice/pdf_invoice_email_debug')) {
            Mage::log($helper->includeInvoicePdfInEmail(), null, 'email_pdf.log');
        }
        if ($helper->includeInvoicePdfInEmail()) {
            /* Check if the sending e-mail is the for invoice confirmation */
            $storeId = $this->getStoreId();
            if (Mage::getStoreConfig('sales_pdf/invoice/pdf_invoice_email_debug')) {
                Mage::log($this->getTemplateId(), null, 'email_pdf.log');
                Mage::log(Mage::getStoreConfig('sales_email/invoice/template', $storeId), null, 'email_pdf.log');
            }
            if ($this->getTemplateId() == Mage::getStoreConfig('sales_email/invoice/template', $storeId)
                || $this->getTemplateId() == Mage::getStoreConfig('sales_email/invoice/guest_template', $storeId)) {

                if (Mage::getStoreConfig('sales_pdf/invoice/pdf_invoice_email_debug')) {
                    Mage::log('Exist template.', null, 'email_pdf.log');
                }
                $templateParams = $this->getTemplateParams();
                if (isset($templateParams['invoice'])) {
                    $invoice = $templateParams['invoice'];
                    $invoices[] = $invoice;
                    if (Mage::getStoreConfig('sales_pdf/invoice/pdf_invoice_email_debug')) {
                        Mage::log(count($invoices), null, 'email_pdf.log');
                    }
                    if (count($invoices)) {
                        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                        $filename = $invoice->getIncrementId() . '.pdf';
                        if (Mage::getStoreConfig('sales_pdf/invoice/pdf_invoice_email_debug')) {
                            Mage::log(count($pdf), null, 'email_pdf.log');
                            Mage::log($filename, null, 'email_pdf.log');
                        }
                        $this->addAttachment($emailTemplate, $pdf, $filename);
                    }
                }
            }
        }

        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailInfo = array_pop($this->_emailInfos);
            // Handle "Bcc" recipients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());
            // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
            $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
                ->setQueue($this->getQueue())
                ->sendTransactional(
                    $this->getTemplateId(),
                    $this->getSender(),
                    $emailInfo->getToEmails(),
                    $emailInfo->getToNames(),
                    $this->getTemplateParams(),
                    $this->getStoreId()
                );
        }
        return $this;
    }

    public function addAttachment($template, Zend_pdf $pdf, $filename) {

        try {
            $file = $pdf->render();
            $attachment = $template->getMail()->createAttachment($file);


            if (Mage::getStoreConfig('sales_pdf/invoice/pdf_invoice_email_debug')) {
                Mage::log(is_object($attachment), null, 'email_pdf.log');
            }
            if (!is_object($attachment)) {
                return $this;
            }

            $attachment->type = 'application/pdf';
            $attachment->filename = $filename;
        } catch (Zend_Pdf_Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }
}
