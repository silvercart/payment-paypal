$CustomHtmlFormInitOutput
<% with PaymentMethod %>
    <% if ErrorOccured %>
<div class="alert alert-error box error clearfix">
    <h3><% _t('SilvercartPaymentPaypal.AnErrorOccurred') %></h3>
    <ul>
        <% loop ErrorList %>
        <li>{$error}</li>
        <% end_loop %>
    </ul>
    <p><%t SilvercartPaymentPaypal.AnErrorOccurredText PaymentStepLink=$CurrentPage.PaymentStepLink LastStepLink=$CurrentPage.LastStepLink %></p>
    <a class="silvercart-button btn btn-primary" href="{$CurrentPage.PaymentStepLink}"><% _t('SilvercartPaymentPaypal.AnErrorOccurredOtherPayment') %></a>
    <br/>
    <br/>
    <a class="silvercart-button btn btn-primary" href="{$CurrentPage.LastStepLink}"><% _t('SilvercartPaymentPaypal.AnErrorOccurredTryAgain') %></a>
</div>
<br/>
    <% end_if %>
<% end_with %>